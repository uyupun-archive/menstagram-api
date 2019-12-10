<?php

namespace App\UseCases;

use App\Models\Like;
use App\Models\Post;

/**
 * いいねした投稿一覧
 *
 * Class FetchUserLikesUseCase
 * @package App\UseCases
 */
class FetchUserLikesUseCase
{
    /**
     * @param $userId
     * @param null $postId
     * @param null $type
     * @return Post[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function __invoke($userId, $postId = null, $type = null)
    {
        $query = Post::with(['user:id,screen_name,avatar']);

        if (is_null($postId) && is_null($type))                             $query->latest('id');
        else if (!is_null($postId) && (is_null($type) || $type === 'new'))  $query->where('id', '>=', $postId);
        else if (!is_null($postId) && $type === 'old')                      $query->where('id', '<=', $postId);

        $posts = $query
                    ->whereHas('likes', function ($query) use ($userId) {
                        $query->where('user_id', $userId);
                    })
                    ->limit(10)
                    ->get();

        $posts = collect($posts)->map(function ($v, $k) use ($userId) {
            $like = Like::where('user_id', $userId)->where('post_id', $v->id)->first();
            $isLiked = true;
            if (collect($like)->isEmpty()) $isLiked = false;

            return collect($v)
                        ->put('is_liked', $isLiked)
                        ->except(['user_id']);
        });

        if (is_null($postId) && is_null($type)) $posts = $posts->reverse()->values();

        return $posts;
    }
}
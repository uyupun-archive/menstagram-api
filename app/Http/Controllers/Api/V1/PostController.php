<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostDetailRequest;
use App\Http\Requests\PostRequest;
use App\Http\Requests\PostTextRequest;
use App\UseCases\FetchPostDetailUseCase;
use App\UseCases\PostTextUseCase;
use App\UseCases\PostUseCase;
use App\UseCases\StoreImagesUseCase;
use App\UseCases\TakeAccessTokenUseCase;
use App\UseCases\TakeUserByAccessTokenUseCase;

/**
 * 投稿系API
 *
 * Class PostController
 * @package App\Http\Controllers\Api\V1
 */
class PostController extends Controller
{
    /**
     * 画像投稿
     *
     * @param PostRequest $request
     * @param StoreImagesUseCase $storeImagesUseCase
     * @param TakeAccessTokenUseCase $takeAccessTokenUseCase
     * @param TakeUserByAccessTokenUseCase $takeUserByAccessTokenUseCase
     * @param PostUseCase $postUseCase
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function post(PostRequest $request,
                         StoreImagesUseCase $storeImagesUseCase,
                         TakeAccessTokenUseCase $takeAccessTokenUseCase,
                         TakeUserByAccessTokenUseCase $takeUserByAccessTokenUseCase,
                         PostUseCase $postUseCase)
    {
        $filePaths = $storeImagesUseCase($request);
        $accessToken = $takeAccessTokenUseCase();
        $userId = $takeUserByAccessTokenUseCase($accessToken)->id;
        $response = $postUseCase($userId, $filePaths);

        return response($response, 200);
    }

    /**
     * テキスト投稿
     *
     * @param PostTextRequest $request
     * @param TakeAccessTokenUseCase $takeAccessTokenUseCase
     * @param TakeUserByAccessTokenUseCase $takeUserByAccessTokenUseCase
     * @param PostTextUseCase $postTextUseCase
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function text(PostTextRequest $request,
                         TakeAccessTokenUseCase $takeAccessTokenUseCase,
                         TakeUserByAccessTokenUseCase $takeUserByAccessTokenUseCase,
                         PostTextUseCase $postTextUseCase)
    {
        $accessToken = $takeAccessTokenUseCase();
        $userId = $takeUserByAccessTokenUseCase($accessToken)->id;
        // TODO: バリデーション化したい
        if (!$postTextUseCase($userId, $request)) return response('{}', 400);

        return response('{}', 200);
    }

    /**
     * 投稿に対するいいね
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function like()
    {
        return response([], 200);
    }

    /**
     * 投稿に対するいいねを外す
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function unlike()
    {
        return response([], 200);
    }

    /**
     * 投稿の詳細を見る
     *
     * @param PostDetailRequest $request
     * @param FetchPostDetailUseCase $useCase
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function detail(PostDetailRequest $request, FetchPostDetailUseCase $useCase)
    {
        $response = $useCase($request->post_id);
        // TODO: バリデーション化したい
        if (collect($response)->isEmpty()) return response('{}', 400);
        return response($response, 200);
    }

    /**
     * 投稿にいいねしたユーザー一覧を見る
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function liker()
    {
        $response =  [
            [
                'id' =>  1,
                'user' => [
                    'user_id' => 'test_mock',
                    'screen_name' => 'ダミーデータさん',
                    'avater' => 'https://placehold.jp/150x150.png?text=%E3%82%A2%E3%82%A4%E3%82%B3%E3%83%B3',
                ]
            ]
        ];

        return response($response, 200);
    }
}

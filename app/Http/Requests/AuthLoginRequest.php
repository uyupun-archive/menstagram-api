<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * ユーザーのログイン
 *
 * Class AuthLoginRequest
 * @package App\Http\Requests
 */
class AuthLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id'  => ['required', 'regex:/^[a-zA-Z0-9_]+$/', 'between:1,16', 'exists:users', ],
            'password' => ['required', 'string', 'min:8', ],
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'user_id.required'  => config('errors.user.user_id.required'),
            'user_id.regex'     => config('errors.user.user_id.regex'),
            'user_id.between'   => config('errors.user.user_id.between'),
            'user_id.exists'    => config('errors.user.user_id.exists'),

            'password.required' => config('errors.user.password.required'),
            'password.string'   => config('errors.user.password.string'),
            'password.min'      => config('errors.user.password.min'),
        ];
    }

    /**
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator)
    {
        $response['errors'] = $validator->errors()->toArray();

        throw new HttpResponseException(
            response($response, 400)
        );
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ShareEndRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * リクエストに対するバリデーションルールを定義するメソッド。
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'share_user_end' => [
                'required',
                'email',
                // usersテーブルに、emailが存在しているかどうかの判定
                Rule::exists('users', 'email')->where(function (Builder $query) {
                    return $query
                        // emailが自分自身のものかどうかの判定
                        ->where('id', '!=', Auth::id());
                }),
            ],
        ];
    }

    /**
     * バリデーションエラーメッセージを定義するメソッド。
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'share_user_end.required' => 'メールアドレスが、入力されていません。共有停止できません。',
            'share_user_end.email' => 'メールアドレスを、入力してください。共有停止できません。',
            'share_user_end.exists' => '指定されたメールアドレスのユーザーが見つかりません。また自分のものです。共有停止できません。',
        ];
    }
}

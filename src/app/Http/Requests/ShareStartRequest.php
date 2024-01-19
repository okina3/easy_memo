<?php

namespace App\Http\Requests;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ShareStartRequest extends FormRequest
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
            'share_user_start' => [
                'required',
                'email',
                // usersテーブルに、emailが存在しているかどうかの判定
                Rule::exists('users', 'email')->where(function (Builder $query) {
                    return $query
                        // emailが自分自身のものかどうかの判定
                        ->where('id', '!=', Auth::id())
                        ->whereNull('deleted_at');
                }),
            ],
            'edit_access' => 'required',
        ];
    }

    /**
     * バリデーションエラーメッセージを定義するメソッド。
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'share_user_start.required' => 'メールアドレスが、入力されていません。共有できません。',
            'share_user_start.email' => 'メールアドレスを、入力してください。共有できません。',
            'share_user_start.exists' => '指定されたメールアドレスのユーザーが見つかりません。また自分のものです。共有できません。',
            'edit_access.required' => '編集の許可が、選択されていません。',
        ];
    }
}

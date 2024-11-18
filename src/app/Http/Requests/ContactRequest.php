<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
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
            'subject' => 'string|max:25',
            'message' => 'string|max:1000',
        ];
    }

    /**
     * バリデーションエラーメッセージを定義するメソッド。
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'subject.string' => '件名が、入力されていません。また、文字列で指定してください。',
            'subject.max' => '件名は、25文字以内で入力してください。',
            'message.string' => 'お問い合わせ内容が、入力されていません。また、文字列で指定してください。',
            'message.max' => 'お問い合わせ内容は、1000文字以内にしてください。',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadMemoRequest extends FormRequest
{
    /**
     * @return bool
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
            'title' => 'string|max:25',
            'content' => 'required|string|max:1000',
            'new_tag' => 'string|nullable|max:25|unique:tags,name',
        ];
    }

    /**
     * バリデーションエラーメッセージを定義するメソッド。
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'title.string' => 'タイトルが空です。また、文字列で指定してください。',
            'title.max' => 'タイトルは、25文字以内で入力してください。',
            'content.required' => 'メモの内容が、入力されていません。',
            'content.max' => '文字数は、1000文字以内にしてください。',
            'new_tag.max' => 'タグは、25文字以内で入力してください。',
            'new_tag.unique' => 'このタグは、すでに登録されています。',
        ];
    }
}

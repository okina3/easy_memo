<?php

namespace Tests\User\Unit\Requests;

use App\Http\Requests\UploadMemoRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\User\TestCase;

class UploadMemoRequestTest extends TestCase
{
    use RefreshDatabase;

    private UploadMemoRequest $request;

    /**
     * テスト前の初期設定（各テストメソッドの実行前に毎回呼び出される）
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        // UploadMemoRequestのインスタンスを作成、テスト用のリクエストオブジェクトを初期化
        $this->request = new UploadMemoRequest();
    }

    /**
     * 指定されたデータを用いてUploadMemoRequestのインスタンスを作成。
     * テスト用に、リクエストにデータをマージするメソッド
     *
     * @param array $data
     * @return UploadMemoRequest
     */
    private function createUploadMemoRequest(array $data): UploadMemoRequest
    {
        // 受け取ったデータをリクエストオブジェクトに統合
        $this->request->merge($data);
        return $this->request;
    }


    /**
     * リクエストが承認されるかどうかを確認するテスト
     *
     * @return void
     */
    public function testAuthorizeReturnsTrue()
    {
        // リクエストが常に承認されることを検証、trueを返すことを確認
        $this->assertTrue($this->request->authorize());
    }

    /**
     * リクエストデータが正しくバリデートされるかを確認するテスト
     *
     * @return void
     */
    public function testRulesValidation()
    {
        // テスト用データ
        $data = ['title' => 'テストタイトル', 'content' => 'テストメモの内容', 'new_tag' => 'テスト新しいタグ',];

        // リクエストを作成
        $request = $this->createUploadMemoRequest($data);
        // バリデータを作成
        $validator = Validator::make($request->all(), $request->rules());

        // バリデーションが成功することを確認
        $this->assertTrue($validator->passes());
    }

    /**
     * バリデーションエラーメッセージが正しく設定されているかを確認するテスト
     *
     * @return void
     */
    public function testMessagesMethod()
    {
        // リクエストからバリデーションメッセージを取得
        $messages = $this->request->messages();

        // 期待されるバリデーションメッセージを定義
        $expectedMessages = [
            'title.string' => 'タイトルが空です。また、文字列で指定してください。',
            'title.max' => 'タイトルは、25文字以内で入力してください。',
            'content.string' => 'メモの内容が空です。また、文字列で指定してください。',
            'content.max' => '文字数は、1000文字以内にしてください。',
            'new_tag.max' => 'タグは、25文字以内で入力してください。',
            'new_tag.unique' => 'このタグは、すでに登録されています。',
        ];

        // 取得したメッセージが期待されるものと一致することを確認
        $this->assertEquals($expectedMessages, $messages);
    }
}
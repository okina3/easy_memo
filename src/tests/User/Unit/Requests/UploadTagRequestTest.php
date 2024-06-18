<?php

namespace Tests\User\Unit\Requests;

use App\Http\Requests\UploadTagRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\User\TestCase;

class UploadTagRequestTest extends TestCase
{
    use RefreshDatabase;

    private UploadTagRequest $request;

    /**
     * テスト前の初期設定（各テストメソッドの実行前に毎回呼び出される）
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        // UploadTagRequestのインスタンスを作成、テスト用のリクエストオブジェクトを初期化
        $this->request = new UploadTagRequest();
    }

    /**
     * 指定されたデータを用いてUploadTagRequestのインスタンスを作成。
     * テスト用に、リクエストにデータをマージするメソッド
     *
     * @param array $data
     * @return UploadTagRequest
     */
    private function createUploadTagRequest(array $data): UploadTagRequest
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
        $data = ['new_tag' => 'テストタグ'];

        // リクエストを作成
        $request = $this->createUploadTagRequest($data);
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
            'new_tag.string' => 'タグが、入力されていません。また、文字列で指定してください。',
            'new_tag.max' => 'タグは、25文字以内で入力してください。',
            'new_tag.unique' => 'このタグは、すでに登録されています。',
        ];

        // 取得したメッセージが期待されるものと一致することを確認
        $this->assertEquals($expectedMessages, $messages);
    }
}
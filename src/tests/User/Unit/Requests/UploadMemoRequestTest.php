<?php

namespace Tests\User\Unit\Requests;

use App\Http\Requests\UploadMemoRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\User\TestCase;

class UploadMemoRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * テスト前の初期設定（各テストメソッドの実行前に毎回呼び出される）
     *
     * @return void
     */
    protected function setUp(): void
    {
        // 親クラスのsetUpメソッドを呼び出し
        parent::setUp();
        // ユーザーを作成
        $user = User::factory()->create();
        // 認証済みのユーザーを返す
        $this->actingAs($user);
    }

    /**
     * UploadMemoRequestのインスタンスを作成
     * @return UploadMemoRequest
     */
    private function createUploadMemoRequest(): UploadMemoRequest
    {
        // UploadMemoRequestのインスタンスを返す
        return new UploadMemoRequest();
    }

    /**
     * authorizeメソッドが、常にtrueを返すことを検証するテスト
     * @return void
     */
    public function testAuthorizeReturnsTrue()
    {
        // UploadMemoRequestのインスタンスを初期化
        $request = $this->createUploadMemoRequest();

        // authorize() メソッドが常に true を返すことを確認
        $this->assertTrue($request->authorize());
    }

    /**
     * バリデーションが、正しく機能することを確認するテスト
     * @return void
     */
    public function testRulesValidation()
    {
        // バリデーション用のデータを設定
        $data = [
            'title' => 'テストタイトル',
            'content' => 'テストメモの内容',
            'new_tag' => 'テスト新しいタグ'
        ];
        // UploadMemoRequestのインスタンスを初期化
        $request = $this->createUploadMemoRequest();
        // データをマージしてバリデータを作成
        $validator = Validator::make($request->merge($data)->all(), $request->rules());

        // バリデーションが成功することを確認
        $this->assertTrue($validator->passes());
    }

    /**
     * バリデーションが、失敗することを確認するテスト
     * @return void
     */
    public function testErrorRulesValidation()
    {
        // バリデーション用のデータを設定（タイトルが、25文字以上）
        $data = [
            'title' => 'テストタイトル、テストタイトル、テストタイトル、テストタイトル',
            'content' => 'テストメモの内容',
            'new_tag' => 'テスト新しいタグ'
        ];
        // UploadMemoRequestのインスタンスを初期化
        $request = $this->createUploadMemoRequest();
        // データをマージしてバリデータを作成
        $validator = Validator::make($request->merge($data)->all(), $request->rules());

        // バリデーションが失敗することを確認
        $this->assertFalse($validator->passes());
    }

    /**
     * バリデーションエラーメッセージが、正しく設定されていることを確認するテスト
     * @return void
     */
    public function testMessagesMethod()
    {
        // UploadMemoRequestのインスタンスを初期化
        $request = $this->createUploadMemoRequest();

        // リクエストから、バリデーションメッセージを取得
        $messages = $request->messages();
        // 期待されるバリデーションメッセージを定義
        $expectedMessages = [
            'title.string' => 'タイトルが空です。また、文字列で指定してください。',
            'title.max' => 'タイトルは、25文字以内で入力してください。',
            'content.string' => 'メモの内容が空です。また、文字列で指定してください。',
            'content.max' => '文字数は、1000文字以内にしてください。',
            'new_tag.max' => 'タグは、25文字以内で入力してください。',
            'new_tag.unique' => 'このタグは、すでに登録されています。'
        ];

        // 取得したメッセージが、期待されるバリデーションメッセージと一致することを確認
        $this->assertEquals($expectedMessages, $messages);
    }
}

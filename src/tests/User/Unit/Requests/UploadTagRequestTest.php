<?php

namespace Tests\User\Unit\Requests;

use App\Http\Requests\UploadTagRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\User\TestCase;

class UploadTagRequestTest extends TestCase
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
     * UploadTagRequestのインスタンスを作成
     * @return UploadTagRequest
     */
    private function createUploadTagRequest(): UploadTagRequest
    {
        // UploadTagRequestのインスタンスを返す
        return new UploadTagRequest();
    }

    /**
     * authorizeメソッドが、常にtrueを返すことを検証するテスト
     * @return void
     */
    public function testAuthorizeReturnsTrue()
    {
        // UploadTagRequestのインスタンスを初期化
        $request = $this->createUploadTagRequest();

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
            'new_tag' => 'テストタグ'
        ];
        // UploadTagRequestのインスタンスを初期化
        $request = $this->createUploadTagRequest();
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
        // バリデーション用のデータを設定（タグ名が、25文字以上）
        $data = [
            'new_tag' => 'テストタグ、テストタグ、テストタグ、テストタグ、テストタグ'
        ];
        // UploadTagRequestのインスタンスを初期化
        $request = $this->createUploadTagRequest();
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
        // UploadTagRequestのインスタンスを初期化
        $request = $this->createUploadTagRequest();

        // リクエストから、バリデーションメッセージを取得
        $messages = $request->messages();
        // 期待されるバリデーションメッセージを定義
        $expectedMessages = [
            'new_tag.string' => 'タグが、入力されていません。また、文字列で指定してください。',
            'new_tag.max' => 'タグは、25文字以内で入力してください。',
            'new_tag.unique' => 'このタグは、すでに登録されています。'
        ];

        // 取得したメッセージが、期待されるバリデーションメッセージと一致することを確認
        $this->assertEquals($expectedMessages, $messages);
    }
}

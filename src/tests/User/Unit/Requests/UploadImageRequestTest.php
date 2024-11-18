<?php

namespace Tests\User\Unit\Requests;

use App\Http\Requests\UploadImageRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Tests\User\TestCase;

class UploadImageRequestTest extends TestCase
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
     * UploadImageRequestのインスタンスを作成
     * @return UploadImageRequest
     */
    private function createUploadImageRequest(): UploadImageRequest
    {
        // UploadImageRequestのインスタンスを返す
        return new UploadImageRequest();
    }

    /**
     * authorizeメソッドが、常にtrueを返すことを検証するテスト
     * @return void
     */
    public function testAuthorizeReturnsTrue()
    {
        // UploadImageRequestのインスタンスを初期化
        $request = $this->createUploadImageRequest();

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
            'images' => UploadedFile::fake()->image('test_image.jpg')->size(1024)
        ];
        // UploadImageRequestのインスタンスを初期化
        $request = $this->createUploadImageRequest();
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
        // バリデーション用のデータを設定（拡張子が、txt）
        $data = [
            'images' => UploadedFile::fake()->image('test_image.txt')->size(1024)
        ];
        // UploadImageRequestのインスタンスを初期化
        $request = $this->createUploadImageRequest();
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
        // UploadImageRequestのインスタンスを初期化
        $request = $this->createUploadImageRequest();

        // リクエストから、バリデーションメッセージを取得
        $messages = $request->messages();
        // 期待されるバリデーションメッセージを定義
        $expectedMessages = [
            'images.required' => '画像が指定されていません。',
            'images.image' => '指定されたファイルが画像ではありません。',
            'images.mimes' => '指定された拡張子(jpg/jpeg/png)ではありません。',
            'images.max' => 'ファイルサイズは2MB以内にしてください。'
        ];

        // 取得したメッセージが、期待されるバリデーションメッセージと一致することを確認
        $this->assertEquals($expectedMessages, $messages);
    }
}

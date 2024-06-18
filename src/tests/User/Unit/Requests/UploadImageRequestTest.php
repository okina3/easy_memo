<?php

namespace Tests\User\Unit\Requests;

use App\Http\Requests\UploadImageRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\User\TestCase;
use Illuminate\Http\UploadedFile;

class UploadImageRequestTest extends TestCase
{
    use RefreshDatabase;

    private UploadImageRequest $request;

    /**
     * テスト前の初期設定（各テストメソッドの実行前に毎回呼び出される）
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        // UploadImageRequestのインスタンスを作成、テスト用のリクエストオブジェクトを初期化
        $this->request = new UploadImageRequest();
    }

    /**
     * 指定されたデータを用いてUploadImageRequestのインスタンスを作成。
     * テスト用に、リクエストにデータをマージするメソッド
     *
     * @param array $data
     * @return UploadImageRequest
     */
    private function createUploadImageRequest(array $data): UploadImageRequest
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
        // テスト用データとして、ファイルを作成
        $data = ['images' => UploadedFile::fake()->image('test_image.jpg')->size(1024)];

        // リクエストを作成
        $request = $this->createUploadImageRequest($data);
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
            'images.required' => '画像が指定されていません。',
            'images.image' => '指定されたファイルが画像ではありません。',
            'images.mimes' => '指定された拡張子(jpg/jpeg/png)ではありません。',
            'images.max' => 'ファイルサイズは2MB以内にしてください。',
        ];

        // 取得したメッセージが期待されるものと一致することを確認
        $this->assertEquals($expectedMessages, $messages);
    }
}
<?php

namespace Tests\User\Unit\Requests;

use App\Http\Requests\ContactRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\User\TestCase;

class ContactRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * テスト前の初期設定（各テストメソッドの実行前に毎回呼び出される）
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
     * ContactRequestのインスタンスを作成
     * @return ContactRequest
     */
    private function createContactRequest(): ContactRequest
    {
        // ContactRequestのインスタンスを返す
        return new ContactRequest();
    }

    /**
     * authorizeメソッドが、常にtrueを返すことを検証するテスト
     * @return void
     */
    public function testAuthorizeReturnsTrue()
    {
        // ContactRequestのインスタンスを初期化
        $request = $this->createContactRequest();

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
            'subject' => 'テスト件名',
            'message' => 'テストメッセージ'
        ];
        // ContactRequestのインスタンスを初期化
        $request = $this->createContactRequest();
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
        // バリデーションの用データを設定（件名が、25文字以上）
        $data = [
            'subject' => 'テスト件名、テスト件名、テスト件名、テスト件名、テスト件名、テスト件名',
            'message' => 'テストメッセージ'
        ];
        // ContactRequestのインスタンスを初期化
        $request = $this->createContactRequest();
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
        // ContactRequestのインスタンスを初期化
        $request = $this->createContactRequest();

        // リクエストから、バリデーションメッセージを取得
        $messages = $request->messages();
        // 期待されるバリデーションメッセージを定義
        $expectedMessages = [
            'subject.string' => '件名が、入力されていません。また、文字列で指定してください。',
            'subject.max' => '件名は、25文字以内で入力してください。',
            'message.string' => 'お問い合わせ内容が、入力されていません。また、文字列で指定してください。',
            'message.max' => 'お問い合わせ内容は、1000文字以内にしてください。'
        ];

        // 取得したメッセージが、期待されるバリデーションメッセージと一致することを確認
        $this->assertEquals($expectedMessages, $messages);
    }
}

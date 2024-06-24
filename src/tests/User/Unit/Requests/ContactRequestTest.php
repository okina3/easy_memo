<?php

namespace Tests\User\Unit\Requests;

use App\Http\Requests\ContactRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\User\TestCase;

class ContactRequestTest extends TestCase
{
    use RefreshDatabase;

    private ContactRequest $request;

    /**
     * テスト前の初期設定（各テストメソッドの実行前に毎回呼び出される）
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        // ContactRequestのインスタンスを作成、テスト用のリクエストオブジェクトを初期化
        $this->request = new ContactRequest();
    }

    /**
     * 指定されたデータを用いてContactRequestのインスタンスを作成。
     * テスト用に、リクエストにデータをマージするメソッド
     *
     * @param array $data
     * @return ContactRequest
     */
    private function createContactRequest(array $data): ContactRequest
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
        $data = ['subject' => 'テスト件名', 'message' => 'テストメッセージ'];

        // リクエストを作成
        $request = $this->createContactRequest($data);
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
            'subject.string' => '件名が、入力されていません。また、文字列で指定してください。',
            'subject.max' => '件名は、25文字以内で入力してください。',
            'message.string' => 'お問い合わせ内容が、入力されていません。また、文字列で指定してください。',
            'message.max' => 'お問い合わせ内容は、1000文字以内にしてください。',
        ];

        // 取得したメッセージが期待されるものと一致することを確認
        $this->assertEquals($expectedMessages, $messages);
    }
}

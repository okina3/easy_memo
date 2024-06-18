<?php

namespace Tests\User\Unit\Requests;

use App\Http\Requests\ShareEndRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Tests\User\TestCase;

class ShareEndRequestTest extends TestCase
{
    use RefreshDatabase;

    private ShareEndRequest $request;

    /**
     * テスト前の初期設定（各テストメソッドの実行前に毎回呼び出される）
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        // ShareEndRequestのインスタンスを作成、テスト用のリクエストオブジェクトを初期化
        $this->request = new ShareEndRequest();
    }

    /**
     * 指定されたデータを用いてShareEndRequestのインスタンスを作成。
     * テスト用に、リクエストにデータをマージするメソッド
     *
     * @param array $data
     * @return ShareEndRequest
     */
    private function createShareEndRequest(array $data): ShareEndRequest
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
        User::factory()->create(['email' => 'test@example.com']);
        $data = [
            'share_user_end' => 'test@example.com'
        ];

        // テストユーザーを認証
        $authUser = User::factory()->create();
        Auth::login($authUser);

        // リクエストを作成
        $request = $this->createShareEndRequest($data);
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
            'share_user_end.required' => 'メールアドレスが、入力されていません。共有停止できません。',
            'share_user_end.email' => 'メールアドレスを、入力してください。共有停止できません。',
            'share_user_end.exists' => '指定されたメールアドレスのユーザーが見つかりません。また自分のものです。共有停止できません。',
        ];

        // 取得したメッセージが期待されるものと一致することを確認
        $this->assertEquals($expectedMessages, $messages);
    }
}
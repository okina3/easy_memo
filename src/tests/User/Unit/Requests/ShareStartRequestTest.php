<?php

namespace Tests\User\Unit\Requests;

use App\Http\Requests\ShareStartRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Tests\User\TestCase;

class ShareStartRequestTest extends TestCase
{
    use RefreshDatabase;

    private ShareStartRequest $request;

    /**
     * テスト前の初期設定（各テストメソッドの実行前に毎回呼び出される）
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        // ShareStartRequestのインスタンスを作成、テスト用のリクエストオブジェクトを初期化
        $this->request = new ShareStartRequest();
    }

    /**
     * 指定されたデータを用いてShareStartRequestのインスタンスを作成。
     * テスト用に、リクエストにデータをマージするメソッド
     *
     * @param array $data
     * @return ShareStartRequest
     */
    private function createShareStartRequest(array $data): ShareStartRequest
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
            'share_user_start' => 'test@example.com',
            'edit_access' => 'true'
        ];

        // テストユーザーを認証
        $authUser = User::factory()->create();
        Auth::login($authUser);

        // リクエストを作成
        $request = $this->createShareStartRequest($data);
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
            'share_user_start.required' => 'メールアドレスが、入力されていません。共有できません。',
            'share_user_start.email' => 'メールアドレスを、入力してください。共有できません。',
            'share_user_start.exists' => '指定されたメールアドレスのユーザーが見つかりません。また自分のものです。共有できません。',
            'edit_access.required' => '編集の許可が、選択されていません。',
        ];

        // 取得したメッセージが期待されるものと一致することを確認
        $this->assertEquals($expectedMessages, $messages);
    }
}

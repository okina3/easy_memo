<?php

namespace Tests\User\Unit\Requests;

use App\Http\Requests\ShareStartRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
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
        // 親クラスのsetUpメソッドを呼び出し
        parent::setUp();
        // ユーザーを作成
        $user = User::factory()->create();
        // 認証済みのユーザーを返す
        $this->actingAs($user);
    }

    /**
     * ShareStartRequestのインスタンスを作成
     * @return ShareStartRequest
     */
    private function createShareStartRequest(): ShareStartRequest
    {
        // ShareStartRequestのインスタンスを返す
        return new ShareStartRequest();
    }

    /**
     * authorizeメソッドが、常にtrueを返すことを検証するテスト
     * @return void
     */
    public function testAuthorizeReturnsTrue()
    {
        // ShareStartRequestのインスタンスを初期化
        $request = $this->createShareStartRequest();

        // authorize() メソッドが常に true を返すことを確認
        $this->assertTrue($request->authorize());
    }

    /**
     * バリデーションが、正しく機能することを確認するテスト
     * @return void
     */
    public function testRulesValidation()
    {
        // バリデーション用のユーザーを作成
        User::factory()->create(['email' => 'test@example.com']);
        // バリデーション用のデータを設定
        $data = [
            'share_user_start' => 'test@example.com',
            'edit_access' => 'true'
        ];
        // ShareStartRequestのインスタンスを初期化
        $request = $this->createShareStartRequest();
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
        // バリデーション用のユーザーを作成（メールアドレスが、文字列）
        User::factory()->create(['email' => 'かきくけこ']);
        // バリデーション用のデータを設定
        $data = [
            'share_user_start' => 'かきくけこ',
            'edit_access' => 'true'
        ];
        // ShareStartRequestのインスタンスを初期化
        $request = $this->createShareStartRequest();
        // データをマージしてバリデータを作成
        $validator = Validator::make($request->merge($data)->all(), $request->rules());

        // バリデーションが失敗することを確認
        $this->assertFalse($validator->passes());
    }

    /**
     * バリデーションエラーメッセージが正しく設定されているかを確認するテスト
     *
     * @return void
     */
    public function testMessagesMethod()
    {
        // ShareStartRequestのインスタンスを初期化
        $request = $this->createShareStartRequest();

        // リクエストから、バリデーションメッセージを取得
        $messages = $request->messages();
        // 期待されるバリデーションメッセージを定義
        $expectedMessages = [
            'share_user_start.required' => 'メールアドレスが、入力されていません。共有できません。',
            'share_user_start.email' => 'メールアドレスを、入力してください。共有できません。',
            'share_user_start.exists' => '指定されたメールアドレスのユーザーが見つかりません。また自分のものです。共有できません。',
            'edit_access.required' => '編集の許可が、選択されていません。'
        ];

        // 取得したメッセージが、期待されるバリデーションメッセージと一致することを確認
        $this->assertEquals($expectedMessages, $messages);
    }
}

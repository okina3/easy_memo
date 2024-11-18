<?php

namespace Tests\Admin\Unit\Models;

use App\Models\Admin;
use App\Notifications\Admin\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\Admin\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    /**
     * テスト前の初期設定（各テストメソッドの実行前に毎回呼び出される）
     * @return void
     */
    protected function setUp(): void
    {
        // 親クラスのsetUpメソッドを呼び出し
        parent::setUp();
        // 管理者ユーザーを作成
        $this->admin = Admin::factory()->create();
        // 管理者ユーザーを認証
        $this->actingAs($this->admin, 'admin');
    }

    /**
     * 管理者パスワードリセット通知のテスト
     */
    public function testSendPasswordResetNotification()
    {
        // 通知を偽装する
        Notification::fake();
        // ダミートークンを設定
        $token = 'dummytoken123';
        // パスワードリセット通知をユーザーに送信
        $this->admin->sendPasswordResetNotification($token);

        // 指定された管理者に対してResetPasswordNotificationが送信されたかを確認
        Notification::assertSentTo($this->admin,
            ResetPasswordNotification::class, fn($notification) => $notification->token === $token);
    }
}

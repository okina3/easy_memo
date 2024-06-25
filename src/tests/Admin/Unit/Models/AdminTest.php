<?php

namespace Tests\Admin\Unit\Models;

use App\Models\Admin;
use App\Notifications\Admin\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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
        parent::setUp();
        // テスト用ユーザー（管理者）作成
        $this->admin = Admin::factory()->create();
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

    /**
     * パスワードのハッシュ処理が正しく機能しているかのテスト
     */
    public function testPasswordIsHashed()
    {
        // テストユーザーを作成
        $admin = Admin::create(['name' => 'テスト管理者', 'email' => 'admin@example.com', 'password' => 'plainpassword',]);

        // パスワードがハッシュされていることを確認
        $this->assertTrue(Hash::check('plainpassword', $admin->password));
        // ハッシュされているため、元のパスワードと一致しないことを確認
        $this->assertNotEquals('plainpassword', $admin->password);
    }
}

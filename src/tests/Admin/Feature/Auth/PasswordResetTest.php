<?php

namespace Tests\Admin\Feature\Auth;

use App\Models\User;

// use Illuminate\Auth\Notifications\ResetPassword;
use App\Notifications\User\ResetPasswordNotification as ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\Admin\TestCase;

class PasswordResetTest extends TestCase
{
    // use RefreshDatabase;

    // /**
    //  * パスワードリセットリンク画面が正常に表示されることをテスト。
    //  * @return void
    //  */
    // public function test_reset_password_link_screen_can_be_rendered(): void
    // {
    //     $response = $this->get('/forgot-password');

    //     $response->assertStatus(200);
    // }

    // /**
    //  * パスワードリセットリンクのリクエストが正常に処理されることをテスト。
    //  * Notification::fake() を使用して、実際にメールを送信せずに通知をテスト。
    //  * @return void
    //  */
    // public function test_reset_password_link_can_be_requested(): void
    // {
    //     Notification::fake();

    //     $user = User::factory()->create();

    //     $this->post('/forgot-password', ['email' => $user->email]);

    //     Notification::assertSentTo($user, ResetPassword::class);
    // }

    // /**
    //  * パスワードリセット画面が正しく表示されることをテスト。
    //  * 通知に含まれるトークンを使用して、リセット画面にアクセスし、ステータス200を確認。
    //  * @return void
    //  */
    // public function test_reset_password_screen_can_be_rendered(): void
    // {
    //     Notification::fake();

    //     $user = User::factory()->create();

    //     $this->post('/forgot-password', ['email' => $user->email]);

    //     Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
    //         $response = $this->get('/reset-password/' . $notification->token);

    //         $response->assertStatus(200);

    //         return true;
    //     });
    // }

    // /**
    //  * 有効なトークンを使用してパスワードをリセットできることをテスト。
    //  * フォームに必要なデータを送信し、エラーがないことを確認。
    //  * @return void
    //  */
    // public function test_password_can_be_reset_with_valid_token(): void
    // {
    //     Notification::fake();

    //     $user = User::factory()->create();

    //     $this->post('/forgot-password', ['email' => $user->email]);

    //     Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
    //         $response = $this->post('/reset-password', [
    //             'token' => $notification->token,
    //             'email' => $user->email,
    //             'password' => 'password',
    //             'password_confirmation' => 'password',
    //         ]);

    //         $response->assertSessionHasNoErrors();

    //         return true;
    //     });
    // }
}

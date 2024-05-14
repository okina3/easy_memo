<?php

namespace Tests\Admin\Feature\Auth;

use App\Models\Admin;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Admin\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ログイン画面が正常に表示されるかテスト。
     * @return void
     */
    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('admin/login');

        $response->assertStatus(200);
    }

    /**
     * ユーザーがログイン画面を使用して認証できることを確認。
     * @return void
     */
    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = Admin::factory()->create();

        $response = $this->post('admin/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated('admin');
        $response->assertRedirect(RouteServiceProvider::ADMIN_HOME);
    }

    /**
     * 無効なパスワードでユーザーが認証できないことをテスト。
     * @return void
     */
    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = Admin::factory()->create();

        $this->post('admin/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest('admin');
    }

    /**
     * ユーザーがログアウトできることをテスト。
     * @return void
     */
    public function test_users_can_logout(): void
    {
        $user = Admin::factory()->create();

        $response = $this->actingAs($user,'admin')->post('admin/logout');

        $this->assertGuest('admin');
        $response->assertRedirect('admin/');
    }
}

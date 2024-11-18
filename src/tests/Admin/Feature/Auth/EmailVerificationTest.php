<?php

namespace Tests\Admin\Feature\Auth;

use App\Models\Admin;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\Admin\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * メール確認画面が正常に表示されることをテスト。
     * @return void
     */
    public function test_email_verification_screen_can_be_rendered(): void
    {
        $admin = Admin::factory()->create([
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($admin, 'admin')->get('admin/verify-email');

        $response->assertStatus(200);
    }

    /**
     * メールが確認済みの管理者が、リダイレクトされることをテスト。
     * @return void
     */
    public function test_verified_user_is_redirected(): void
    {
        // メールアドレスが確認済みの管理者を作成
        $admin = Admin::factory()->create([
            'email_verified_at' => now(),
        ]);
        // 確認済みのメールアドレスを持つ管理者で、リクエストを送信
        $response = $this->actingAs($admin, 'admin')->get('/admin/verify-email');

        // 管理者が、管理者用ホームページにリダイレクトされることを確認
        $response->assertRedirect(RouteServiceProvider::ADMIN_HOME);
    }

    /**
     * ユーザーのメールが確認されるフローをテスト。
     * @return void
     */
    public function test_email_can_be_verified(): void
    {
        $admin = Admin::factory()->create([
            'email_verified_at' => null,
        ]);

        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'admin.verification.verify',
            now()->addMinutes(60),
            ['id' => $admin->id, 'hash' => sha1($admin->email)]
        );

        $response = $this->actingAs($admin, 'admin')->get($verificationUrl);

        Event::assertDispatched(Verified::class);
        $this->assertTrue($admin->fresh()->hasVerifiedEmail());
        $response->assertRedirect(RouteServiceProvider::ADMIN_HOME . '?verified=1');
    }

    /**
     * メールが確認済みの管理者が、リダイレクトされることをテスト。
     * @return void
     */
    public function test_already_verified_email_redirect(): void
    {
        // メールアドレスが確認済みの管理者を作成
        $admin = Admin::factory()->create([
            'email_verified_at' => now(),
        ]);
        // 検証用のURLを作成
        $verificationUrl = URL::temporarySignedRoute(
            'admin.verification.verify',
            now()->addMinutes(60),
            ['id' => $admin->id, 'hash' => sha1($admin->email)]
        );
        // 確認済みのメールアドレスを持つ管理者で、リクエストを送信
        $response = $this->actingAs($admin, 'admin')->get($verificationUrl);

        // 管理者が、管理者用ホームページにリダイレクトされることを確認
        $response->assertRedirect(RouteServiceProvider::ADMIN_HOME . '?verified=1');
    }

    /**
     * 無効なハッシュを使用した場合にメールが確認されないことをテスト。
     * @return void
     */
    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        $admin = Admin::factory()->create([
            'email_verified_at' => null,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'admin.verification.verify',
            now()->addMinutes(60),
            ['id' => $admin->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs($admin, 'admin')->get($verificationUrl);

        $this->assertFalse($admin->fresh()->hasVerifiedEmail());
    }
}

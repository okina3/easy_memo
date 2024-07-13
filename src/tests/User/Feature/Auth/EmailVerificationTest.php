<?php

namespace Tests\User\Feature\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\User\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * メール確認画面が正常に表示されることをテスト。
     * @return void
     */
    public function test_email_verification_screen_can_be_rendered(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($user)->get('/verify-email');

        $response->assertStatus(200);
    }

    /**
     * メールが確認済みのユーザーが、リダイレクトされることをテスト。
     * @return void
     */
    public function test_verified_user_is_redirected(): void
    {
        // メールアドレスが確認済みのユーザーを作成
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        // 確認済みのメールアドレスを持つユーザーで、リクエストを送信
        $response = $this->actingAs($user)->get('/verify-email');

        // ユーザーが、ユーザー用ホームページにリダイレクトされることを確認
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    /**
     * ユーザーのメールが確認されるフローをテスト。
     * @return void
     */
    public function test_email_can_be_verified(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'user.verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        Event::assertDispatched(Verified::class);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect(RouteServiceProvider::HOME . '?verified=1');
    }

    /**
     * メールが確認済みのユーザーが、リダイレクトされることをテスト。
     * @return void
     */
    public function test_already_verified_email_redirect(): void
    {
        // メールアドレスが確認済みのユーザーを作成
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        // 検証用のURLを作成
        $verificationUrl = URL::temporarySignedRoute(
            'user.verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );
        // 確認済みのメールアドレスを持つユーザーで、リクエストを送信
        $response = $this->actingAs($user)->get($verificationUrl);

        // ユーザーが、ユーザー用ホームページにリダイレクトされることを確認
        $response->assertRedirect(RouteServiceProvider::HOME . '?verified=1');
    }

    /**
     * 無効なハッシュを使用した場合にメールが確認されないことをテスト。
     * @return void
     */
    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'user.verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs($user)->get($verificationUrl);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }
}

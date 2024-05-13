<?php

namespace Tests\User\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\User\TestCase;

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ユーザーのパスワードを更新できることをテスト。
     * 正しい現在のパスワードが提供された場合、新しいパスワードに更新し、プロファイルページにリダイレクトされることを確認。
     * また、新しいパスワードがデータベースに正しくハッシュされているかを検証。
     * @return void
     * @throws \JsonException
     */
    public function test_password_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertTrue(Hash::check('new-password', $user->refresh()->password));
    }

    /**
     * パスワード更新の際、正確な現在のパスワードが必要であることをテスト。
     * 間違った現在のパスワードが提供された場合、エラーが発生しプロファイルページにリダイレクトされることを確認。
     * @return void
     */
    public function test_correct_password_must_be_provided_to_update_password(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('updatePassword', 'current_password')
            ->assertRedirect('/profile');
    }
}

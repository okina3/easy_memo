<?php

namespace Tests\Admin\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Admin\TestCase;

class ProfileTest extends TestCase
{
    /**
     * クラスの最初のテストメソッドが実行される前に一度だけ実行されるセットアップメソッド
     * テストクラス全体をスキップするメソッド。
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        self::markTestSkipped('このテストクラスはスキップされました。');
    }

    // use RefreshDatabase;

    // public function test_profile_page_is_displayed(): void
    // {
    //     $admin = Admin::factory()->create();

    //     $response = $this
    //         ->actingAs($admin, 'admin')
    //         ->get('/admin/profile');

    //     $response->assertOk();
    // }

    // public function test_profile_information_can_be_updated(): void
    // {
    //     $admin = Admin::factory()->create();

    //     $response = $this
    //         ->actingAs($admin, 'admin')
    //         ->patch('/admin/profile', [
    //             'name' => 'Test Admin',
    //             'email' => 'test@example.com',
    //         ]);

    //     $response
    //         ->assertSessionHasNoErrors()
    //         ->assertRedirect('/admin/profile');

    //     $admin->refresh();

    //     $this->assertSame('Test Admin', $admin->name);
    //     $this->assertSame('test@example.com', $admin->email);
    //     $this->assertNull($admin->email_verified_at);
    // }

    // public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    // {
    //     $admin = Admin::factory()->create();

    //     $response = $this
    //         ->actingAs($admin, 'admin')
    //         ->patch('/admin/profile', [
    //             'name' => 'Test Admin',
    //             'email' => $admin->email,
    //         ]);

    //     $response
    //         ->assertSessionHasNoErrors()
    //         ->assertRedirect('/admin/profile');

    //     $this->assertNotNull($admin->refresh()->email_verified_at);
    // }

    // public function test_admin_can_delete_their_account(): void
    // {
    //     $admin = Admin::factory()->create();

    //     $response = $this
    //         ->actingAs($admin, 'admin')
    //         ->delete('/admin/profile', [
    //             'password' => 'password',
    //         ]);

    //     $response
    //         ->assertSessionHasNoErrors()
    //         ->assertRedirect('/');

    //     $this->assertGuest();
    //     $this->assertNull($admin->fresh());
    // }

    // public function test_correct_password_must_be_provided_to_delete_account(): void
    // {
    //     $admin = Admin::factory()->create();

    //     $response = $this
    //         ->actingAs($admin, 'admin')
    //         ->from('/admin/profile')
    //         ->delete('/admin/profile', [
    //             'password' => 'wrong-password',
    //         ]);

    //     $response
    //         ->assertSessionHasErrorsIn('adminDeletion', 'password')
    //         ->assertRedirect('/admin/profile');

    //     $this->assertNotNull($admin->fresh());
    // }
}

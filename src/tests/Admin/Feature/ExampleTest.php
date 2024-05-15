<?php

namespace Tests\Admin\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Admin;
use Tests\Admin\TestCase;

class ExampleTest extends TestCase
{
    /**
     *  アプリケーションが成功したレスポンスを返すことをテスト。
     *
     * ユーザーを作成し、そのユーザーで認証した状態で、アプリケーションのホームページにアクセスし、HTTPステータスコードが200であることを確認。
     * @return void
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $user = Admin::factory()->create();

        // $response = $this->get('/');
        $response = $this->actingAs($user, 'admin')
            ->get('admin/');

        $response->assertOk();
    }
}

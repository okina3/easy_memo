<?php

namespace Tests\Admin\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Admin\TestCase;

class UsersControllerTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;
    private User $user;

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
        // ユーザーを作成
        $this->user = User::factory()->create();
        // 管理者ユーザーを認証
        $this->actingAs($this->admin, 'admin');
    }

    /**
     * 全ユーザー、また、検索したユーザーを表示するテスト
     * @return void
     */
    public function testIndexUsersController()
    {
        // ユーザーを5人作成
        User::factory()->count(5)->create();
        // 最初のユーザーのメールアドレスをキーワードとして設定
        $keyword = $this->user->email;

        // 全ユーザー、また、検索したユーザーを表示するリクエストを送信
        $response = $this->get(route('admin.index', ['keyword' => $keyword]));

        // レスポンスが正しいビューを返すことを確認
        $response->assertStatus(200);
        $response->assertViewIs('admin.users.index');

        // ビューに渡されるデータが正しいか確認
        $response->assertViewHas('all_users', function ($viewUsers) use ($keyword) {
            return $viewUsers->contains('email', $keyword);
        });
    }

    /**
     * ユーザーのサービス利用を停止するテスト
     * @return void
     */
    public function testDestroyUsersController()
    {
        // ユーザーを1人作成
        $user = User::factory()->create();

        // ユーザーのサービス利用を停止するリクエストを送信
        $response = $this->delete(route('admin.destroy', ['userId' => $user->id]), ['userId' => $user->id]);

        // ユーザーがソフトデリートされたことを確認
        $this->assertSoftDeleted('users', ['id' => $user->id]);

        // レスポンスが正しいリダイレクト先を指していることを確認
        $response->assertRedirect(route('admin.index'));
        $response->assertSessionHas(['message' => 'ユーザーのサービス利用を停止しました', 'status' => 'alert']);
    }
}
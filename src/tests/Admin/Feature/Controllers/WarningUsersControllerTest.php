<?php

namespace Tests\Admin\Feature\Controllers;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\Admin\TestCase;

class WarningUsersControllerTest extends TestCase
{
    use RefreshDatabase;

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
        $admin = Admin::factory()->create();
        // 管理者を認証
        $this->actingAs($admin, 'admin');
    }

    /**
     * ソフトデリートされたユーザーを作成するヘルパーメソッド
     * @param int $count ソフトデリートされたユーザーの作成数
     * @return Collection 作成されたユーザーのコレクション
     */
    private function createTrashedUsers(int $count): Collection
    {
        // 指定された数のソフトデリートされたユーザーを作成する
        return User::factory()->count($count)->create(['deleted_at' => now()]);
    }

    /**
     * 全てのソフトデリートしたユーザー一覧が、正しく表示されることをテスト
     * @return void
     */
    public function testAllIndexWarningUsersController()
    {
        // 3件のソフトデリートされたユーザーを作成
        $users = $this->createTrashedUsers(3);

        // 全ユーザーを表示する為に、リクエストを送信
        $response = $this->get(route('admin.warning.index'));

        // レスポンスが 'admin.warningUsers.index' ビューを返すことを確認
        $response->assertStatus(200);
        $response->assertViewIs('admin.warningUsers.index');

        // ビューに渡されるデータが正しいか確認
        $response->assertViewHas('all_warning_users', function ($viewUsers) use ($users) {
            // ビューに渡されるユーザーが、3件であり、かつ、ユーザーのID配列も、一致することを確認
            return $viewUsers->count() === 3 && $viewUsers->pluck('id')->toArray() === $users->pluck('id')->toArray();
        });
    }

    /**
     * 絞り込んだソフトデリートしたユーザー一覧が、正しく表示されることをテスト
     * @return void
     */
    public function testSearchIndexWarningUsersController()
    {
        // 3件のソフトデリートされたユーザーを作成
        $users = $this->createTrashedUsers(3);

        // 最初のユーザーのメールアドレスをキーワードとして設定
        $keyword = $users->first()->email;

        // 検索したユーザーを表示する為に、リクエストを送信
        $response = $this->get(route('admin.warning.index', ['keyword' => $keyword]));

        // レスポンスが 'admin.warningUsers.index' ビューを返すことを確認
        $response->assertStatus(200);
        $response->assertViewIs('admin.warningUsers.index');

        // ビューに渡されるデータが正しいか確認
        $response->assertViewHas('all_warning_users', function ($viewUsers) use ($users, $keyword) {
            // キーワード（メールアドレス）でユーザーを絞り込み
            $filteredUsers = $users->filter(function ($users) use ($keyword) {
                return stripos($users->email, $keyword) !== false;
            });
            // ビューに渡されるユーザーと、絞り込まれたユーザーの数が同じ、かつ、ID配列も一致することを確認
            return $viewUsers->count() === $filteredUsers->count() &&
                $viewUsers->pluck('id')->toArray() === $filteredUsers->pluck('id')->toArray();
        });
    }

    /**
     * ソフトデリートしたユーザーが、正しく元に戻されることをテスト
     * @return void
     */
    public function testUndoWarningUsersController()
    {
        // 1件のソフトデリートされたユーザーを作成
        $user = $this->createTrashedUsers(1)->first();

        // ソフトデリートしたユーザーを、元に戻す為に、リクエストを送信
        $response = $this->patch(route('admin.warning.undo'), ['userId' => $user->id]);

        // ユーザーが元に戻されたことを確認
        $this->assertDatabaseHas('users', ['id' => $user->id, 'deleted_at' => null,]);

        // レスポンスが 'admin.warning.index' リダイレクト先を指していることを確認
        $response->assertRedirect(route('admin.warning.index'));
        $response->assertSessionHas(['message' => 'ユーザーのサービス利用を再開しました', 'status' => 'info']);
    }

    /**
     * ソフトデリートしたユーザーが、正しく完全削除されることをテスト
     * @return void
     */
    public function testDestroyWarningUsersController()
    {
        // 1件のソフトデリートされたユーザーを作成
        $user = $this->createTrashedUsers(1)->first();

        // ソフトデリートしたユーザーを、完全に削除する為に、リクエストを送信
        $response = $this->delete(route('admin.warning.destroy'), ['userId' => $user->id]);

        // ユーザーが完全に削除されたことを確認
        $this->assertDatabaseMissing('users', ['id' => $user->id]);

        // レスポンスが 'admin.warning.index' リダイレクト先を指していることを確認
        $response->assertRedirect(route('admin.warning.index'));
        $response->assertSessionHas(['message' => 'ユーザーの情報を完全に削除しました。', 'status' => 'alert']);
    }
}

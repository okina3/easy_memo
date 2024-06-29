<?php

namespace Tests\Admin\Feature;

use App\Models\Admin;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\Admin\TestCase;

class UsersControllerTest extends TestCase
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
     * ユーザーを作成するヘルパーメソッド
     * @param int $count ユーザーの作成数
     * @return Collection 作成されたユーザーのコレクション
     */
    private function createUsers(int $count): Collection
    {
        // 指定された数のユーザーを作成する
        return User::factory()->count($count)->create();
    }

    /**
     * 全てのユーザーの一覧表示が正しく行われることをテスト
     * @return void
     */
    public function testAllIndexUsersController()
    {
        // 3件のユーザーを作成
        $users = $this->createUsers(3);

        // 全ユーザーを表示する為に、リクエストを送信
        $response = $this->get(route('admin.index'));

        // レスポンスが 'admin.users.index' ビューを返すことを確認
        $response->assertStatus(200);
        $response->assertViewIs('admin.users.index');

        // ビューに渡されるデータが正しいか確認
        $response->assertViewHas('all_users', function ($viewUsers) use ($users) {
            // ビューで取得したユーザー数が3であり、かつ、ビューで取得したユーザーと作成したユーザーの、最初のIDが一致することを確認
            return $viewUsers->count() === 3 && $viewUsers->first()->id === $users->first()->id;
        });
    }

    /**
     * 検索したユーザーの一覧表示が正しく行われることをテスト
     * @return void
     */
    public function testSearchIndexUsersController()
    {
        // 3件のユーザーを作成
        $users = $this->createUsers(3);

        // 最初のユーザーのメールアドレスをキーワードとして設定
        $keyword = $users->first()->email;

        // 検索したユーザーを表示するリクエストを送信
        $response = $this->get(route('admin.index', ['keyword' => $keyword]));

        // レスポンスが 'admin.users.index' ビューを返すことを確認
        $response->assertStatus(200);
        $response->assertViewIs('admin.users.index');

        // ビューに渡されるデータが正しいか確認
        $response->assertViewHas('all_users', function ($viewUsers) use ($users, $keyword) {
            // ビューから取得したユーザーをコレクションに変換
            $viewUsers = collect($viewUsers);
            // キーワード（メールアドレス）でユーザーを絞り込み
            $filteredUsers = $users->filter(function ($users) use ($keyword) {
                return stripos($users->email, $keyword) !== false;
            });
            // 絞り込まれたユーザーの数とIDが、ビューで取得したユーザーと一致するかを確認
            return $viewUsers->count() === $filteredUsers->count() &&
                $viewUsers->pluck('id')->sort()->values()->all() === $filteredUsers->pluck('id')->sort()->values()->all();
        });
    }

    /**
     * ユーザーが正しくサービス利用を停止されることをテスト
     * @return void
     */
    public function testDestroyUsersController()
    {
        // 1件のユーザーを作成
        $user = $this->createUsers(1)->first();

        // ユーザーのサービス利用を停止するリクエストを送信
        $response = $this->delete(route('admin.destroy'), ['userId' => $user->id]);

        // ユーザーがソフトデリートされたことを確認
        $this->assertSoftDeleted('users', ['id' => $user->id]);

        // レスポンスが 'admin.index' リダイレクト先を指していることを確認
        $response->assertRedirect(route('admin.index'));
        $response->assertSessionHas(['message' => 'ユーザーのサービス利用を停止しました', 'status' => 'alert']);
    }

    /**
     * ユーザーがサービス利用停止時のエラーハンドリングをテスト
     * @return void
     */
    public function testErrorDestroyUsersController()
    {
        // 1件のユーザーを作成
        $user = $this->createUsers(1)->first();

        // DB::transactionメソッドが呼び出されると、一度だけ例外をスローするように設定
        DB::shouldReceive('transaction')->once()->andThrow(new Exception('DBエラー'));

        // Log::errorメソッドが呼び出されるときに、例外がログに記録されることを確認
        Log::shouldReceive('error')->once()->with(Mockery::type(Exception::class));

        // 例外がスローされることを期待し、そのメッセージが"DBエラー"であることを確認
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('DBエラー');

        // ユーザーのサービス利用を停止するリクエストを送信
        $this->delete(route('admin.destroy'), ['userId' => $user->id]);
    }
}

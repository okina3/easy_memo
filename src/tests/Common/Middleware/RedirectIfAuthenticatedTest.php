<?php

namespace Tests\Common\Middleware;

use App\Http\Middleware\RedirectIfAuthenticated;
use App\Models\Admin;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Tests\Common\TestCase;

class RedirectIfAuthenticatedTest extends TestCase
{
    use RefreshDatabase;

    /**
     * テスト前の初期設定（各テストメソッドの実行前に毎回呼び出される）
     * @return void
     */
    protected function setUp(): void
    {
        // 親クラスのsetUpメソッドを呼び出し
        parent::setUp();

        // テスト用のルートを登録
        $this->registerRoutes();
    }

    /**
     * ルートの登録（各ルートは 'RedirectIfAuthenticated' ミドルウェアを使用）
     * @return void
     */
    protected function registerRoutes(): void
    {
        Route::middleware(['web', RedirectIfAuthenticated::class])->group(function () {
            // 管理者用ダッシュボードへのルート
            Route::get('/admin/dashboard', function () {
                return '管理者ダッシュボード';
            })->name('admin.dashboard');

            // ユーザー用ダッシュボードへのルート
            Route::get('/user/dashboard', function () {
                return 'ユーザーダッシュボード';
            })->name('user.dashboard');
        });
    }

    /**
     * 認証済みの管理者が管理者ページにアクセスした際のリダイレクトテスト
     * @return void
     */
    public function testAdminRedirectIfAuthenticated()
    {
        // 管理者として認証
        $admin = Admin::factory()->create();
        Auth::guard('admin')->login($admin);

        // 管理者用ダッシュボードへのリクエスト
        $response = $this->get('/admin/dashboard');

        // 管理者ホームページにリダイレクトされることを確認
        $response->assertRedirect(RouteServiceProvider::ADMIN_HOME);
    }

    /**
     * 認証済みのユーザーがユーザーページにアクセスした際のリダイレクトテスト
     * @return void
     */
    public function testUserRedirectIfAuthenticated()
    {
        // ユーザーとして認証
        $user = User::factory()->create();
        Auth::guard('users')->login($user);

        // ユーザーダッシュボードへのリクエスト
        $response = $this->get('/user/dashboard');

        // ユーザーホームページにリダイレクトされることを確認
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    /**
     * 未認証のユーザーが管理者ページにアクセスした際のリダイレクトなしテスト
     * @return void
     */
    public function testUnauthenticatedUserAccessingAdminPage()
    {
        // 管理者ダッシュボードへのリクエスト
        $response = $this->get('/admin/dashboard');

        // 正常に管理者ダッシュボードにアクセスできることを確認
        $response->assertOk();
        $response->assertSee('管理者ダッシュボード');
    }

    /**
     * 未認証のユーザーがユーザーページにアクセスした際のリダイレクトなしテスト
     * @return void
     */
    public function testUnauthenticatedUserAccessingUserPage()
    {
        // ユーザーダッシュボードへのリクエスト
        $response = $this->get('/user/dashboard');

        // 正常にユーザーダッシュボードにアクセスできることを確認
        $response->assertOk();
        $response->assertSee('ユーザーダッシュボード');
    }
}

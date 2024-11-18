<?php

namespace Tests\Common\Middleware;

use App\Http\Middleware\Authenticate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\Common\TestCase;

class AuthenticateMiddlewareTest extends TestCase
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
     * ルートの登録（各ルートは 'Authenticate' ミドルウェアを使用）
     * @return void
     */
    protected function registerRoutes(): void
    {
        Route::middleware(['web', Authenticate::class])->group(function () {
            // 管理者用ダッシュボードへのルート
            Route::get('/admin/dashboard', function () {
                return '管理者ダッシュボード';
            })->name('admin.dashboard');

            // ユーザー用ダッシュボードへのルート
            Route::get('/user/dashboard', function () {
                return 'ユーザーダッシュボード';
            })->name('user.dashboard');

            // 管理者用ログインページへのルート
            Route::get('/admin/login', function () {
                return '管理者ログイン';
            })->name('admin.login');

            // ユーザー用ログインページへのルート
            Route::get('/user/login', function () {
                return 'ユーザーログイン';
            })->name('user.login');
        });
    }

    /**
     * 未認証のユーザーが管理者ページにアクセスした際のリダイレクトテスト
     * @return void
     */
    public function testRedirectToAdminLogin()
    {
        // 管理者用ダッシュボードへのリクエスト
        $response = $this->get('/admin/dashboard');

        // 管理者ログインページにリダイレクトされることを確認
        $response->assertRedirect(route('admin.login'));
    }

    /**
     * 未認証のユーザーがユーザーページにアクセスした際のリダイレクトテスト
     * @return void
     */
    public function testRedirectToUserLogin()
    {
        // ユーザーダッシュボードへのリクエスト
        $response = $this->get('/user/dashboard');

        // ユーザーログインページにリダイレクトされることを確認
        $response->assertRedirect(route('user.login'));
    }
}

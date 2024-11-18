<?php

namespace Tests\Common\Providers;

use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\Request;
use Tests\Common\TestCase;

class SessionCookieTest extends TestCase
{
    /**
     * 管理者用のセッションCookieが正しく設定されているかをテスト
     * @return void
     */
    public function testAdminSessionCookieIsSet()
    {
        // 管理者用のリクエストをシミュレート
        $request = Request::create('/admin/test');
        $this->app['request'] = $request;

        // AppServiceProviderを再度ブートして設定を反映
        (new AppServiceProvider($this->app))->boot();

        // セッションのcookie名が管理者用に「admin」に設定されているかを確認
        $this->assertEquals('admin', config('session.cookie'));
    }
}

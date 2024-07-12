<?php

namespace Tests\Admin\Feature\Config;

use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\Request;
use Tests\Admin\TestCase;

class SessionCookieTest extends TestCase
{
    public function testAdminSessionCookieIsSet()
    {
        // 管理者用のリクエストをシミュレート
        $request = Request::create('/admin/test');
        $this->app['request'] = $request;

        // AppServiceProvider を再度ブートストラップ
        (new AppServiceProvider($this->app))->boot();

        // セッションのcookie名が管理者用に設定されているかを確認
        $this->assertEquals('admin', config('session.cookie'));
    }

    public function testRegularSessionCookieIsSet()
    {
        // 通常のリクエストをシミュレート
        $request = Request::create('/user/test');
        $this->app['request'] = $request;

        // AppServiceProvider を再度ブートストラップ
        (new AppServiceProvider($this->app))->boot();

        // セッションのcookie名が通常用に設定されているかを確認
        $this->assertEquals('user', config('session.cookie'));
    }
}

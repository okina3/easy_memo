<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class Authenticate extends Middleware
{
    protected string $admin_route = 'admin.login';
    protected string $user_route = 'user.top';

    /**
     * ユーザが認証されていない場合に、リダイレクトされるパスを切り分けるメソッド。
     * @param Request $request
     * @return string|null
     */
    protected function redirectTo(Request $request): ?string
    {
        if (!$request->expectsJson()) {
            if (Route::is('admin.*')) {
                return route($this->admin_route);
            } else {
                return route($this->user_route);
            }
        }
    }
}

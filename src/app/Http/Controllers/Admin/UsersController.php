<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UsersController extends Controller
{
    /**
     * 全ユーザー、また、検索したユーザーを表示するメソッド。
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        // 全ユーザー、また、検索したユーザーを取得
        $users_all = User::availableUserOrder()
            ->searchKeyword($request->keyword)
            ->paginate(5);

        return view('admin.users.index', compact('users_all'));
    }

    /**
     * ユーザーのサービス利用を停止するメソッド。
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        // 選択したユーザーのサービス利用を停止
        User::findOrFail($request->userId)->delete();

        return to_route('admin.index')->with(['message' => 'ユーザーのサービス利用を停止しました', 'status' => 'alert']);
    }
}

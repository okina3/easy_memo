<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UsersController extends Controller
{
    /**
     * ユーザー一覧を表示するメソッド。
     * @return View
     */
    public function index(): View
    {
        // 全ユーザーを取得する
        $users_all = User::all();

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

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WarningUsersController extends Controller
{
    /**
     * 警告したユーザー一覧を表示するメソッド。
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        // 警告したユーザーを取得する
        $warning_users_all = User::onlyTrashed()->availableUserOrder()
        ->searchKeyword($request->keyword)
        ->paginate(5);

        return view('admin.warningUsers.index', compact('warning_users_all'));
    }

    /**
     * 警告したユーザーを元に戻すメソッド。
     * @param Request $request
     * @return RedirectResponse
     */
    public function undo(Request $request): RedirectResponse
    {
        User::onlyTrashed()->availableSelectUser($request)->restore();

        return to_route('admin.warning.index')->with(['message' => 'ユーザーのサービス利用を再開しました', 'status' => 'info']);
    }

    /**
     * 警告したユーザーを完全削除するメソッド。
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        User::onlyTrashed()->availableSelectUser($request)->forceDelete();

        return to_route('admin.warning.index')->with(['message' => 'ユーザーの情報を完全に削除しました。', 'status' => 'alert']);
    }
}

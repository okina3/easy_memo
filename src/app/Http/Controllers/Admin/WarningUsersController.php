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
     * @return View
     */
    public function index(): View
    {
        // 警告したユーザーを取得する
        $warning_users_all = User::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->get();

        return view('admin.warningUsers.index', compact('warning_users_all'));
    }

    /**
     * 警告したユーザーを元に戻すメソッド。
     * @param Request $request
     * @return RedirectResponse
     */
    public function undo(Request $request): RedirectResponse
    {

        return to_route('user.trashed-memo.index')->with(['message' => 'ユーザーのサービス利用を再開しました', 'status' => 'info']);
    }

    /**
     * 警告したユーザーを完全削除するメソッド。
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {

        return to_route('user.trashed-memo.index')->with(['message' => 'ユーザーの情報を完全に削除しました。', 'status' => 'alert']);
    }
}

<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Memo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TrashedMemoController extends Controller
{
    /**
     * ソフトデリートしたメモ一覧を表示するメソッド。
     * @return View
     */
    public function index(): View
    {
        $trashed_memos = Memo::onlyTrashed()
            ->where('user_id', Auth::id())
            ->orderBy('deleted_at', 'desc')
            ->get();

        return view('user.trashedMemos.trashed-memo', compact('trashed_memos'));
    }

    /**
     * ソフトデリートしたメモを元に戻すメソッド。
     * @param Request $request
     * @return RedirectResponse
     */
    public function undo(Request $request): RedirectResponse
    {
        Memo::onlyTrashed()->availableTrashedMemo($request)->restore();

        return to_route('user.trashed-memo.index')->with(['message' => 'メモを元に戻しました。', 'status' => 'info']);
    }

    /**
     * ソフトデリートしたメモを完全削除するメソッド。
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        Memo::onlyTrashed()->availableTrashedMemo($request)->forceDelete();

        return to_route('user.trashed-memo.index')->with(['message' => 'メモを完全に削除しました。', 'status' => 'alert']);
    }
}

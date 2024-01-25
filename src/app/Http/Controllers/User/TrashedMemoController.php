<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Memo;
use App\Services\SessionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrashedMemoController extends Controller
{
    /**
     * ソフトデリートしたメモ一覧を表示するメソッド。
     * @return View
     */
    public function index(): View
    {
        // ブラウザバック対策（値を削除する）
        SessionService::resetBrowserBackSession();
        $trashed_memos = Memo::onlyTrashed()->availableAllTrashedMemos()->get();

        return view('user.trashedMemos.trashed-memo', compact('trashed_memos'));
    }

    /**
     * ソフトデリートしたメモを元に戻すメソッド。
     * @param Request $request
     * @return RedirectResponse
     */
    public function undo(Request $request): RedirectResponse
    {
        Memo::onlyTrashed()->availableSelectTrashedMemo($request->memoId)->restore();

        return to_route('user.trashed-memo.index')->with(['message' => 'メモを元に戻しました。', 'status' => 'info']);
    }

    /**
     * ソフトデリートしたメモを完全削除するメソッド。
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        Memo::onlyTrashed()->availableSelectTrashedMemo($request->memoId)->forceDelete();

        return to_route('user.trashed-memo.index')->with(['message' => 'メモを完全に削除しました。', 'status' => 'alert']);
    }
}

<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Memo;
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
}

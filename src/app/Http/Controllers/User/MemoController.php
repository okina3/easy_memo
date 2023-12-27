<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Services\MemoService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemoController extends Controller
{
    public function __construct()
    {
        // 別のユーザーのメモを見られなくする認証。
        $this->middleware(function (Request $request, Closure $next) {
            MemoService::memoUserCheck($request);
            return $next($request);
        });
    }

    /**
     * メモとタグの一覧を表示するメソッド。
     * @return View
     */
    public function index(): View
    {
        // 全メモ、または検索されたメモを表示する
        $all_memos = MemoService::memoSearchAll();
        // 全タグを取得する
        $all_tags = Tag::availableTagAll()->get();

        return view('user.memos.index', compact('all_memos', 'all_tags'));
    }
}

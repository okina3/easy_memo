<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadMemoRequest;
use App\Models\Image;
use App\Models\Memo;
use App\Models\Tag;
use App\Services\MemoService;
use App\Services\TagService;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

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

    /**
     * メモの新規作成画面を表示するメソッド。
     * @return View
     */
    public function create(): View
    {
        //全タグを取得する
        $all_tags = Tag::availableTagAll()->get();
        //全画像を取得する
        $all_images = Image::availableImageAll()->get();

        return view('user.memos.create', compact('all_tags', 'all_images'));
    }

    /**
     * メモを保存するメソッド。
     * @param UploadMemoRequest $request
     * @return RedirectResponse
     * @throws Throwable
     */
    public function store(UploadMemoRequest $request): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request) {
                //メモを保存
                $memo = Memo::create([
                    'title' => $request->title,
                    'content' => $request->content,
                    'user_id' => Auth::id(),
                ]);
                // 新規タグの入力があれば、各データを保存。
                TagService::tagCreate($request, $memo);
                // 既存のタグと画像の選択があれば、メモに紐付けて中間テーブルに保存
                MemoService::attachRelationship($request, $memo);
            }, 10);
        } catch (Throwable $e) {
            Log::error($e);
            throw $e;
        }
        return to_route('index')->with(['message' => 'メモを登録しました。', 'status' => 'info']);
    }
}

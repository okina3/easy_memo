<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadMemoRequest;
use App\Models\Image;
use App\Models\Memo;
use App\Models\MemoImage;
use App\Models\MemoTag;
use App\Models\Tag;
use App\Services\ImageService;
use App\Services\MemoService;
use App\Services\ShareSettingService;
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
        return to_route('user.index')->with(['message' => 'メモを登録しました。', 'status' => 'info']);
    }

    /**
     *  メモの詳細を表示するメソッド。
     * @param string $id
     * @return View
     */
    public function show(string $id): View
    {
        // 選択したメモを、一件取得
        $choice_memo = Memo::availableMemoInTag($id)->first();
        // 選択したメモに紐づいたタグの名前を取得
        $memo_in_tags = TagService::memoRelationTags($choice_memo, 'name');
        // 選択したメモに紐づいた画像を取得
        $memo_in_images = ImageService::memoRelationImages($choice_memo);
        // 共有されているメモに目印を付ける
        MemoService::sharedCheck($choice_memo);
        // 自分が共有しているメモの、共有状態の情報を取得
        $shared_users = ShareSettingService::shareMemoUserInformation($id);

        return view('user.memos.show', compact('choice_memo', 'memo_in_tags', 'memo_in_images', 'shared_users'));
    }

    /**
     * メモの編集画面を表示するメソッド。
     * @param string $id
     * @return View
     */
    public function edit(string $id): View
    {
        // タグの一覧表示
        $all_tags = Tag::availableTagAll()->get();
        // 全画像を取得する
        $all_images = Image::availableImageAll()->get();
        // 選択したメモを、一件取得。
        $choice_memo = Memo::availableMemoInTag($id)->first();
        // 選択したメモに紐づいたタグのidを取得
        $memo_in_tags = TagService::memoRelationTags($choice_memo, 'id');
        // 選択したメモに紐づいた画像を取得
        $memo_in_images = ImageService::memoRelationImages($choice_memo);
        // 選択したメモに紐づいた画像のidを取得
        $memo_in_images_id = ImageService::memoRelationImagesId($choice_memo);
        // 共有されているメモに目印を付ける
        MemoService::sharedCheck($choice_memo);

        return view(
            'user.memos.edit',
            compact('all_tags', 'all_images', 'choice_memo', 'memo_in_tags', 'memo_in_images_id', 'memo_in_images')
        );
    }

    /**
     * メモの更新画面を表示するメソッド。
     * @param UploadMemoRequest $request
     * @return RedirectResponse
     * @throws Throwable
     */
    public function update(UploadMemoRequest $request): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request) {
                // メモを更新
                $memo = Memo::findOrFail($request->memoId);
                $memo->title = $request->title;
                $memo->content = $request->content;
                $memo->save();
                // 一旦メモとタグを紐付けた中間デーブルのデータを削除
                MemoTag::where('memo_id', $request->memoId)->delete();
                // 一旦メモと画像を紐付けた中間デーブルのデータを削除
                MemoImage::where('memo_id', $request->memoId)->delete();
                // 新規タグの入力があれば、各データを保存。
                TagService::tagCreate($request, $memo);
                // 既存のタグと画像の選択があれば、メモに紐付けて中間テーブルに保存
                MemoService::attachRelationship($request, $memo);
            }, 10);
        } catch (Throwable $e) {
            Log::error($e);
            throw $e;
        }
        return to_route('user.index')->with(['message' => 'メモを更新しました。', 'status' => 'info']);
    }

    /**
     * メモを削除するメソッド。
     * @param Request $request
     * @return RedirectResponse
     * @throws Throwable
     */
    public function destroy(Request $request): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request) {
                // 選択したメモを削除
                Memo::findOrFail($request->memoId)->delete();
                // 選択した全てのメモの共有設定を解除
                ShareSettingService::shareSettingAllDelete($request);
            }, 10);
        } catch (Throwable $e) {
            Log::error($e);
            throw $e;
        }
        return to_route('user.index')->with(['message' => 'メモを削除しました。', 'status' => 'alert']);
    }
}

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
use App\Services\SessionService;
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
            MemoService::checkUserMemo($request);
            return $next($request);
        });
    }

    /**
     * メモとタグの一覧を表示するメソッド。
     * @return View
     */
    public function index(): View
    {
        // ブラウザバック対策（値を削除する）
        SessionService::resetBrowserBackSession();
        // 全メモ、または検索されたメモを表示する
        $all_memos = MemoService::searchMemos();
        // 全タグを取得する
        $all_tags = Tag::availableAllTags()->get();

        return view('user.memos.index', compact('all_memos', 'all_tags'));
    }

    /**
     * メモの新規作成画面を表示するメソッド。
     * @return View
     */
    public function create(): View
    {
        // 全タグを取得する
        $all_tags = Tag::availableAllTags()->get();
        // 全画像を取得する
        $all_images = Image::availableAllImages()->get();
        // ブラウザバック対策（値を持たせる）
        SessionService::setBrowserBackSession();
        // session()->flash('back_button_clicked', encrypt(env('BROWSER_BACK_KEY')));

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
        // ブラウザバック対策（値を確認）
        SessionService::clickBrowserBackSession();
        try {
            DB::transaction(function () use ($request) {
                // メモを保存
                $memo = Memo::create([
                    'title' => $request->title,
                    'content' => $request->content,
                    'user_id' => Auth::id(),
                ]);
                // 新規タグの入力があれば、各データを保存。
                TagService::storeNewTag($request->new_tag, $memo->id);
                // 既存のタグと画像の選択があれば、メモに紐付けて中間テーブルに保存
                MemoService::attachTagsAndImages($request, $memo->id);
            }, 10);
        } catch (Throwable $e) {
            Log::error($e);
            throw $e;
        }
        return to_route('user.index')->with(['message' => 'メモを登録しました。', 'status' => 'info']);
    }

    /**
     *  メモの詳細を表示するメソッド。
     * @param int $id
     * @return View
     */
    public function show(int $id): View
    {
        // 選択したメモを、一件取得
        $select_memo = Memo::availableSelectMemo($id)->first();
        // 選択したメモに紐づいたタグの名前を取得
        $get_memo_tags = TagService::getMemoTags($select_memo->tags, 'name');
        // 選択したメモに紐づいた画像を取得
        $get_memo_images = ImageService::getMemoImages($select_memo->images);
        // 共有されているメモに目印を付ける
        MemoService::checkShared($select_memo);
        // 自分が共有しているメモの、共有状態の情報を取得
        $shared_users = ShareSettingService::checkSharedMemoStatus($id);

        return view('user.memos.show', compact('select_memo', 'get_memo_tags', 'get_memo_images', 'shared_users'));
    }

    /**
     * メモの編集画面を表示するメソッド。
     * @param int $id
     * @return View
     */
    public function edit(int $id): View
    {
        // 全タグの一覧表示
        $all_tags = Tag::availableAllTags()->get();
        // 全画像を取得する
        $all_images = Image::availableAllImages()->get();
        // 選択したメモを、一件取得。
        $select_memo = Memo::availableSelectMemo($id)->first();
        // 選択したメモに紐づいたタグのidを取得
        $get_memo_tags = TagService::getMemoTags($select_memo->tags, 'id');
        // 選択したメモに紐づいた画像を取得
        $get_memo_images = ImageService::getMemoImages($select_memo->images);
        // 選択したメモに紐づいた画像のidを取得
        $get_memo_images_id = ImageService::getMemoImagesId($select_memo->images);
        // 共有されているメモに目印を付ける
        MemoService::checkShared($select_memo);
        // ブラウザバック対策（値を持たせる）
        SessionService::setBrowserBackSession();

        return view('user.memos.edit',
            compact('all_tags', 'all_images', 'select_memo', 'get_memo_tags', 'get_memo_images_id', 'get_memo_images')
        );
    }

    /**
     * メモを更新するメソッド。
     * @param UploadMemoRequest $request
     * @return RedirectResponse
     * @throws Throwable
     */
    public function update(UploadMemoRequest $request): RedirectResponse
    {
        // ブラウザバック対策（値を確認）
        SessionService::clickBrowserBackSession();
        try {
            DB::transaction(function () use ($request) {
                // メモを更新
                $memo = MemoService::updateMemo($request);
                // 一旦メモとタグを紐付けた中間デーブルのデータを削除
                MemoTag::where('memo_id', $request->memoId)->delete();
                // 一旦メモと画像を紐付けた中間デーブルのデータを削除
                MemoImage::where('memo_id', $request->memoId)->delete();
                // 新規タグの入力があれば、各データを保存。
                TagService::storeNewTag($request->new_tag, $memo->id);
                // 既存のタグと画像の選択があれば、メモに紐付けて中間テーブルに保存
                MemoService::attachTagsAndImages($request, $memo->id);
            }, 10);
        } catch (Throwable $e) {
            Log::error($e);
            throw $e;
        }
        return to_route('user.index')->with(['message' => 'メモを更新しました。', 'status' => 'info']);
    }

    /**
     * メモを削除（ソフトデリート）するメソッド。
     * @param Request $request
     * @return RedirectResponse
     * @throws Throwable
     */
    public function destroy(Request $request): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request) {
                // 選択したメモを削除
                Memo::availableSelectMemo($request->memoId)->delete();
                // 選択したメモの全ての共有設定を解除
                ShareSettingService::deleteShareSettingAll($request->memoId);
            }, 10);
        } catch (Throwable $e) {
            Log::error($e);
            throw $e;
        }
        return to_route('user.index')->with(['message' => 'メモをゴミ箱に移動しました。', 'status' => 'alert']);
    }
}

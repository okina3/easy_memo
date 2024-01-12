<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShareEndRequest;
use App\Http\Requests\ShareStartRequest;
use App\Http\Requests\UploadMemoRequest;
use App\Models\Memo;
use App\Models\ShareSetting;
use App\Models\User;
use App\Services\ImageService;
use App\Services\ShareSettingService;
use App\Services\TagService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class ShareSettingController extends Controller
{
    /**
     * 共有メモの一覧を表示するメソッド。
     * @return View
     */
    public function index(): View
    {
        // 一旦全ての共有されたメモを取得
        $share_setting_memos = ShareSetting::availableSharesMemoAll()->get();
        // パラメーターから、全ての共有メモ、ユーザー別の共有メモを、切り分ける。
        $shared_memos = ShareSettingService::searchSharedMemos($share_setting_memos);
        // メモを共有しているユーザー名を取得する。
        $shared_users = ShareSettingService::searchSharedUserName($share_setting_memos);

        return view('user.shareSettings.index', compact('shared_memos', 'shared_users'));
    }

    /**
     * メモを共有する為のメソッド。
     * @param ShareStartRequest $request
     * @return RedirectResponse
     * @throws Throwable
     */
    public function store(ShareStartRequest $request): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request) {
                // メールアドレスから、ユーザーを特定
                $shared_user = User::where('email', $request->share_user_start)->first();
                // 共有設定が、重複していたら、共有設定を、一旦解除する。
                ShareSettingService::resetDuplicateShareSettings($request->memoId, $shared_user->id);
                // ユーザーを特定できたら、DBに保存する
                ShareSetting::create([
                    'sharing_user_id' => $shared_user->id,
                    'memo_id' => $request->memoId,
                    'edit_access' => $request->edit_access,
                ]);
            }, 10);
        } catch (Throwable $e) {
            Log::error($e);
            throw $e;
        }
        return to_route('user.index')->with(['message' => 'メモを共有しました。', 'status' => 'info']);
    }

    /**
     *  共有メモの詳細を表示するメソッド。
     * @param string $id
     * @return View
     */
    public function show(string $id): View
    {
        // 共有されていないメモの詳細を見られなくする
        ShareSettingService::checkSharedMemoShow($id);
        // 選択した共有メモを、一件取得
        $choice_memo = Memo::with('tags.user')->where('id', $id)->first();
        // 選択したメモに紐づいたタグの名前を取得
        $memo_in_tags = TagService::getMemoTags($choice_memo->tags, 'name');
        // 選択したメモに紐づいた画像を取得
        $memo_in_images = ImageService::getMemoImages($choice_memo->images);
        // 選択した共有メモのユーザーを取得
        $choice_user = $choice_memo->user;

        return view('user.shareSettings.show', compact('choice_memo', 'memo_in_tags', 'memo_in_images', 'choice_user'));
    }

    /**
     * 共有メモの編集画面を表示するメソッド。
     * @param string $id
     * @return View
     */
    public function edit(string $id): View
    {
        // 共有、許可されていない、メモの編集をできなくする
        ShareSettingService::checkSharedMemoEdit($id);
        // 選択した共有メモを、一件取得
        $choice_memo = Memo::with('tags.user')->where('id', $id)->first();
        // 選択したメモに紐づいたタグの名前を取得
        $memo_in_tags = TagService::getMemoTags($choice_memo->tags, 'name');
        // 選択したメモに紐づいた画像を取得
        $memo_in_images = ImageService::getMemoImages($choice_memo->images);
        // 選択した共有メモのユーザーを取得
        $choice_user = $choice_memo->user;

        return view('user.shareSettings.edit', compact('choice_memo', 'memo_in_tags', 'memo_in_images', 'choice_user'));
    }

    /**
     * 共有メモの更新画面を表示するメソッド。
     * @param UploadMemoRequest $request
     * @return RedirectResponse
     */
    public function update(UploadMemoRequest $request): RedirectResponse
    {
        $memo = Memo::findOrFail($request->memoId);
        $memo->content = $request->content;
        $memo->save();

        return to_route('user.share-setting.index')->with(['message' => '共有されたメモを更新しました。', 'status' => 'info']);
    }

    /**
     * @param ShareEndRequest $request
     * @return RedirectResponse
     */
    public function destroy(ShareEndRequest $request): RedirectResponse
    {
        // メールアドレスから、ユーザーを特定
        $shared_user = User::where('email', $request->share_user_end)->first();
        //ユーザーを特定できたら、共有を解除する
        ShareSetting::availableSelectSetting($shared_user->id, $request->memoId)->delete();

        return to_route('user.index')->with(['message' => '共有を解除しました。', 'status' => 'alert']);
    }
}

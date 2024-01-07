<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShareEndRequest;
use App\Http\Requests\ShareStartRequest;
use App\Models\ShareSetting;
use App\Models\User;
use App\Services\ShareSettingService;
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
        $shared_memos = ShareSettingService::sharedMemoSearchAll($share_setting_memos);
        // メモを共有しているユーザー名を取得する。
        $shared_users = ShareSettingService::sharedUserSearchAll($share_setting_memos);

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
                ShareSettingService::shareSettingCheck($request, $shared_user);
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
     * @param ShareEndRequest $request
     * @return RedirectResponse
     */
    public function destroy(ShareEndRequest $request): RedirectResponse
    {
        // メールアドレスから、ユーザーを特定
        $shared_user = User::where('email', $request->share_user_end)->first();
        //ユーザーを特定できたら、共有を解除する
        ShareSetting::availableSelectSetting($shared_user, $request)->delete();

        return to_route('user.index')->with(['message' => '共有を解除しました。', 'status' => 'alert']);
    }
}

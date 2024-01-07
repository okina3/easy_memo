<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ShareSetting;
use App\Services\ShareSettingService;
use Illuminate\Http\Request;

use Illuminate\View\View;


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
}

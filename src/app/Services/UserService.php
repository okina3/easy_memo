<?php

namespace App\Services;

use App\Models\ShareSetting;
use App\Models\User;

class UserService
{
    /**
     * 停止ユーザーの、全てのメモ、共有されているメモ、の共有設定を解除するメソッド。
     * @param $request_user_id
     * @return void
     */
    public static function deleteUserShareSettingAll($request_user_id): void
    {
        // 停止ユーザーの全てのメモの共有を解除
        $user_memos = User::with('memos.shareSettings')->availableSelectUser($request_user_id)->first();
        foreach ($user_memos->memos as $user_memo) {
            foreach ($user_memo->shareSettings as $shareSetting) {
                ShareSetting::findOrFail($shareSetting->id)->delete();
            }
        }
        // 停止ユーザーに共有しているメモの共有を解除
        $share_settings = ShareSetting::where('sharing_user_id', $request_user_id)->get();
        foreach ($share_settings as $share_setting) {
            ShareSetting::findOrFail($share_setting->id)->delete();
        }
    }
}

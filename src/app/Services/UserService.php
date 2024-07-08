<?php

namespace App\Services;

use App\Models\ShareSetting;
use App\Models\User;

class UserService
{
    /**
     * 停止ユーザーの、共有されているメモの、共有設定を解除するメソッド。
     * @param int $request_user_id
     * @return void
     */
    public static function deleteUserShareSettingAll(int $request_user_id): void
    {
        // 停止ユーザーが、共有しているメモの共有を解除
        $user_memos = User::with('memos.shareSettings')->availableSelectUser($request_user_id)->first();
        foreach ($user_memos->memos as $user_memo) {
            foreach ($user_memo->shareSettings as $shareSetting) {
                ShareSetting::findOrFail($shareSetting->id)->delete();
            }
        }
        // 停止ユーザーに、共有しているメモの共有を解除
        $share_settings = ShareSetting::where('sharing_user_id', $request_user_id)->get();
        foreach ($share_settings as $share_setting) {
            ShareSetting::findOrFail($share_setting->id)->delete();
        }
    }
}

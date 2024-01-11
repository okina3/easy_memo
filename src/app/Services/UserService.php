<?php

namespace App\Services;

use App\Models\ShareSetting;
use App\Models\User;

class UserService
{
    /**
     * 自分の、全てのメモの共有設定を解除するメソッド。
     * @param [type] $request
     * @return void
     */
    public static function myShareSettingStop($request): void
    {
        $user_memos = User::with('memos.shareSettings')->availableSelectUser($request)->first();
        foreach ($user_memos->memos as $user_memo) {
            foreach ($user_memo->shareSettings as $shareSetting) {
                ShareSetting::findOrFail($shareSetting->id)->delete();
            }
        }
    }

    /**
     * 他のユーザーの、停止ユーザーに共有しているメモの共有を解除するメソッド。
     * @param [type] $request
     * @return void
     */
    public static function youShareSettingStop($request): void
    {
        $share_settings = ShareSetting::where('sharing_user_id', $request->userId)->get();
        foreach ($share_settings as $share_setting) {
            ShareSetting::findOrFail($share_setting->id)->delete();
        }
    }
}

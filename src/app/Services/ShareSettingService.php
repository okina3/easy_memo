<?php

namespace App\Services;

use App\Models\ShareSetting;

class ShareSettingService
{
    /**
     * 自分が共有しているメモの共有状態の情報を取得するメソッド。
     * @param $id
     * @return array
     */
    public static function shareMemoUserInformation($id): array
    {
        // 自分が共有しているメモの情報を、空の配列に追加
        $shared_users = [];
        $share_settings_relation = ShareSetting::availableSettingInUser($id)->get();
        foreach ($share_settings_relation as $share_setting_relation) {
            // ユーザー情報に、編集許可の判定を追加する
            $share_setting_relation->user->access = $share_setting_relation->edit_access;
            $shared_users[] = $share_setting_relation->user;
        }
        return $shared_users;
    }

}

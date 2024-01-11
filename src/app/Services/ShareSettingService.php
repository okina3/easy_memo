<?php

namespace App\Services;

use App\Models\ShareSetting;

class ShareSettingService
{
    /**
     * パラメーターから、全ての共有メモ、ユーザー別の共有メモを、切り分けるメソッド。
     * @param $share_setting_memos
     * @return array
     */
    public static function sharedMemoSearchAll($share_setting_memos): array
    {
        // クエリパラメータを取得。
        $get_url_user_id = \Request::query('user');
        // 結果の共有メモを格納する空の配列
        $shared_memos = [];
        // クエリパラメータがあった場合と、なかった場合の処理。
        if (!empty($get_url_user_id)) {
            // 暗号化を元に戻す
            $decrypted_user_id = decrypt($get_url_user_id);
            // 絞り込んだユーザーの、共有されているメモを、空の配列に追加。
            foreach ($share_setting_memos as $share_setting_memo) {
                // 共有メモ情報に、編集許可の判定を追加する
                $share_setting_memo->memo->access = $share_setting_memo->edit_access;
                // 絞り込んだユーザーの、自分自身に共有されているメモを空の配列に追加
                if ($share_setting_memo->memo->user_id === $decrypted_user_id) {
                    $shared_memos[] = $share_setting_memo->memo;
                }
            }
        } else {
            //全ての共有されたメモを、空の配列に追加。
            foreach ($share_setting_memos as $share_setting_memo) {
                // 共有メモ情報に、編集許可の判定を追加する
                $share_setting_memo->memo->access = $share_setting_memo->edit_access;
                // そのまま、全ての共有されたメモを、空の配列に追加
                $shared_memos[] = $share_setting_memo->memo;
            }
        }
        return $shared_memos;
    }

    /**
     * メモを共有しているユーザー名を取得するメソッド。
     * @param $share_setting_memos
     * @return array
     */
    public static function sharedUserSearchAll($share_setting_memos): array
    {
        // 共有情報から、全ユーザー名を、空の配列に追加
        $shared_users = [];
        foreach ($share_setting_memos as $share_setting_user) {
            // すでに同じ値が存在しない場合に追加
            if (!in_array($share_setting_user->memo->user, $shared_users)) {
                $shared_users[] = $share_setting_user->memo->user;
            }
        }
        return $shared_users;
    }

    /**
     * 共有設定が、重複していたら、共有設定を、一旦解除するメソッド。
     * @param $request
     * @param $shared_user
     * @return void
     */
    public static function shareSettingCheck($request_memo_id, $shared_user_id): void
    {
        // 共有設定が、重複していないか調べる
        $share_setting_exists = ShareSetting::availableSelectSetting($shared_user_id, $request_memo_id)->exists();
        // 重複していたら、共有設定を解除
        if ($share_setting_exists) {
            ShareSetting::availableSelectSetting($shared_user_id, $request_memo_id)->delete();
        }
    }

    /**
     * 共有されていないメモの詳細を見られなくするメソッド。
     * @param $id
     * @return void
     */
    public static function shareShowCheck($id): void
    {
        $share_setting_memo = ShareSetting::availableSettingCheck($id)->first();
        if (!$share_setting_memo || !$share_setting_memo->memo) {
            abort(404);
        }
    }

    /**
     * 共有、許可されていないメモの編集をできなくするメソッド。
     * @param $id
     * @return void
     */
    public static function shareEditCheck($id): void
    {
        $share_setting_memo = ShareSetting::availableSettingCheck($id)->first();
        if (!$share_setting_memo || !($share_setting_memo->edit_access === 1)) {
            abort(404);
        }
    }

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

    /**
     * 選択した全てのメモの共有設定を解除するメソッド。
     * @param $request
     * @return void
     */
    public static function shareSettingAllDelete($request_memo_id): void
    {
        // 選択したメモの全ての共有設定を解除
        $share_settings = ShareSetting::where('memo_id', $request_memo_id)->get();
        foreach ($share_settings as $share_setting) {
            ShareSetting::findOrFail($share_setting->id)->delete();
        }
    }
}

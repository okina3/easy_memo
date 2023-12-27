<?php

namespace App\Services;

use App\Models\Memo;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;

class MemoService
{
    /**
     * 別のユーザーのメモを見られなくする為のメソッド。
     * @param $request
     * @return void
     */
    public static function memoUserCheck($request): void
    {
        // パラメーターを取得
        $id_memo = $request->route()->parameter('memo');
        // 自分自身のメモなのかチェック
        if (!is_null($id_memo)) {
            $memo_relation_user = Memo::findOrFail($id_memo)->user->id;
            if ($memo_relation_user !== Auth::id()) {
                abort(404);
            }
        }
    }

    /**
     * 全メモ、また、検索したメモを一覧表示するメソッド。
     * @return mixed
     */
    public static function memoSearchAll(): mixed
    {
        // クエリパラメータを取得
        $get_url_tag = \Request::query('tag');
        // もしクエリパラメータがあれば、タグから絞り込む
        if (!empty($get_url_tag)) {
            // 絞り込んだタグにリレーションされたメモを含む、タグを取得
            $tag_relation = Tag::availableTagInMemo($get_url_tag)->first();
            // タグにリレーションされたメモを取得
            $memos = $tag_relation->memos;
        } else {
            // 全メモを取得
            $memos = Memo::availableMemoAll()->get();
        }
        // 共有されているメモに目印を付ける
        foreach ($memos as $memo) {
            // メモが共有されているかどうかを確認
            $is_shared = $memo->shareSettings->isNotEmpty();
            // もしメモが共有されている場合、メモに共有中のステータスを追加
            if ($is_shared) {
                $memo->status = "共有中";
            }
        }
        return $memos;
    }
}
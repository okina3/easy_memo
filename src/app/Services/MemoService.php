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
    public static function checkUserMemo($request): void
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
    public static function searchMemos(): mixed
    {
        // クエリパラメータを取得
        $get_url_tag = \Request::query('tag');
        // もしクエリパラメータがあれば、タグから絞り込む
        if (!empty($get_url_tag)) {
            // 絞り込んだタグにリレーションされたメモを含む、タグを取得
            $tag_relation = Tag::availableSelectTag($get_url_tag)->first();
            // タグにリレーションされたメモを取得
            $memos = $tag_relation->memos;
        } else {
            // 全メモを取得
            $memos = Memo::availableAllMemos()->get();
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

    /**
     * メモに紐づいた、既存のタグと画像を、中間テーブルに値を保存するメソッド
     * @param $request
     * @param int $memo_id
     * @return void
     */
    public static function attachTagsAndImages($request, int $memo_id): void
    {
        // 既存タグの選択があれば、メモに紐付けて中間テーブルに保存
        if (!empty($request->tags)) {
            foreach ($request->tags as $tag_number) {
                Memo::findOrFail($memo_id)->tags()->attach($tag_number);
            }
        }
        // 画像の選択があれば、メモに紐付けて中間テーブルに保存
        if (!empty($request->images)) {
            foreach ($request->images as $memo_image) {
                Memo::findOrFail($memo_id)->images()->attach($memo_image);
            }
        }
    }

    /**
     * メモを更新するメソッド。
     * @param $request
     * @return mixed
     */
    public static function updateMemo($request): mixed
    {
        $memo = Memo::availableSelectMemo($request->memoId)->first();
        $memo->title = $request->title;
        $memo->content = $request->content;
        $memo->save();

        return $memo;
    }

    /**
     * 共有されているメモに目印を付けるメソッド。
     * @param $choice_memo
     * @return mixed
     */
    public static function checkShared($choice_memo): mixed
    {
        // メモが共有されているかどうかを確認
        $is_shared = $choice_memo->shareSettings->isNotEmpty();
        // もしメモが共有されている場合、メモに共有中のステータスを追加
        if ($is_shared) {
            $choice_memo->status = "共有中";
        }
        return $choice_memo;
    }
}

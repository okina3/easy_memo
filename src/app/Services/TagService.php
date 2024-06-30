<?php

namespace App\Services;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class TagService
{
    /**
     * 新規タグの保存・更新するメソッド。
     * @param $request_new_tag
     * @param int $memo_id
     * @return void
     */
    public static function storeNewTag($request_new_tag, int $memo_id): void
    {
        // 新規タグの入力があった場合、タグが重複していないか調べる
        $tag_exists = Tag::availableCheckDuplicateTag($request_new_tag)->exists();
        // 新規タグがあり、重複していなければ、タグを保存し、中間テーブルに保存
        if (!empty($request_new_tag) && !$tag_exists) {
            // タグを保存
            $tag = Tag::create([
                'name' => $request_new_tag,
                'user_id' => Auth::id()
            ]);
            // メモとタグの中間テーブルに値を保存
            Tag::findOrFail($tag->id)->memos()->attach($memo_id);
        }
    }

    /**
     * 選択したメモに紐づいた、タグのIDを、配列で取得するメソッド。
     * @param Collection $select_memo_tags
     * @return array
     */
    public static function getMemoTagsId(Collection $select_memo_tags): array
    {
        $memo_relation_tags_id = [];
        foreach ($select_memo_tags as $memo_relation_tag) {
            // メモにリレーションされたタグのidを、配列に追加
            $memo_relation_tags_id[] = $memo_relation_tag->id;
        }
        return $memo_relation_tags_id;
    }

    /**
     * 選択したメモに紐づいた、タグのNameを、配列で取得するメソッド。
     * @param Collection $select_memo_tags
     * @return array
     */
    public static function getMemoTagsName(Collection $select_memo_tags): array
    {
        $memo_relation_tags_name = [];
        foreach ($select_memo_tags as $memo_relation_tag) {
            // メモにリレーションされたタグのidを、配列に追加
            $memo_relation_tags_name[] = $memo_relation_tag->name;
        }
        return $memo_relation_tags_name;
    }
}

<?php

namespace App\Services;

use App\Models\Tag;
use Illuminate\Support\Facades\Auth;

class TagService
{
    /**
     * 新規タグの保存・更新するメソッド。
     * 
     * @param $request
     * @param $memo
     * @return void
     */
    public static function tagCreate($request, $memo): void
    {
        // 新規タグの入力があった場合、タグが重複していないか調べる
        $tag_exists = Tag::availableTagExists($request)->exists();
        // 新規タグがあり、重複していなければ、タグを保存し、中間テーブルに保存
        if (!empty($request->new_tag) && !$tag_exists) {
            // タグを保存
            $tag = Tag::create([
                'name' => $request->new_tag,
                'user_id' => Auth::id()
            ]);
            // メモとタグの中間テーブルに値を保存
            Tag::findOrFail($tag->id)->memos()->attach($memo->id);
        }
    }
}

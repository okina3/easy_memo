<?php

namespace App\Services;

use App\Models\Image;
use Illuminate\Support\Facades\Auth;

class ImageService
{
    /**
     * 別のユーザーの画像を見られなくする為のメソッド。
     * @param $request
     * @return void
     */
    public static function imageUserCheck($request): void
    {
        // パラメーターを取得
        $id_image = $request->route()->parameter('image');
        // 自分自身の画像なのかチェック
        if (!is_null($id_image)) {
            $image_relation_user = Image::findOrFail($id_image)->user->id;
            if ($image_relation_user !== Auth::id()) {
                abort(404);
            }
        }
    }

    /**
     * 選択したメモに紐づいた画像を取得するメソッド
     * @param $choice_memo
     * @return array
     */
    public static function memoRelationImages($choice_memo): array
    {
        // メモにリレーションされた画像を、配列に追加
        $memo_in_images = [];
        foreach ($choice_memo->images as $memo_relation_image) {
            $memo_in_images[] = $memo_relation_image;
        }
        return $memo_in_images;
    }

    /**
     * 選択したメモに紐づいた画像のidを取得するメソッド
     * @param $choice_memo
     * @return array
     */
    public static function memoRelationImagesId($choice_memo): array
    {
        // メモにリレーションされた画像のidを、配列に追加
        $memo_in_images_id = [];
        foreach ($choice_memo->images as $memo_relation_image) {
            $memo_in_images_id[] = $memo_relation_image->id;
        }
        return $memo_in_images_id;
    }
}

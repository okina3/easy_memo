<?php

namespace App\Services;

use App\Models\Image;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

    /**
     * 画像をリサイズして、Laravelのフォルダ内に保存するメソッド。
     * @param $image_file
     * @param $manager
     * @return string
     */
    public static function afterResizingImage($image_file, $manager): string
    {
        // ランダムなファイル名の生成
        $rnd_file_name = uniqid(rand() . '_');
        // 選択画像の拡張子を取得
        $get_extension = $image_file->extension();
        // ランダムなファイル名と拡張子を結合
        $only_one_file_name = $rnd_file_name . '.' . $get_extension;
        // 実際のリサイズ
        $resize_image = $manager->read($image_file)
            ->resize(720, 480)
            ->toJpeg(90);
        // 保存場所とファイル名を指定して、Laravel内に保存
        Storage::put('public/' . $only_one_file_name, $resize_image);
        return $only_one_file_name;
    }

    /**
     * Storageフォルダ内の画像ファイルを削除するメソッド。
     * @param $image
     * @return void
     */
    public static function storageDelete($image): void
    {
        // Storageフォルダ内の画像ファイルを削除
        $file_path = 'public/' . $image->filename;
        if (Storage::exists($file_path)) {
            Storage::delete($file_path);
        }
    }
}

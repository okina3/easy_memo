<?php

namespace App\Services;

use App\Models\Image;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class ImageService
{
    /**
     * 別のユーザーの画像を見られなくする為のメソッド。
     * @param $request
     * @return void
     */
    public static function checkUserImage($request): void
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
     * @param Collection $select_memo_images
     * @return array
     */
    public static function getMemoImages(Collection $select_memo_images): array
    {
        // メモにリレーションされた画像を、配列に追加
        $memo_in_images = [];
        foreach ($select_memo_images as $memo_relation_image) {
            $memo_in_images[] = $memo_relation_image;
        }
        return $memo_in_images;
    }

    /**
     * 選択したメモに紐づいた画像のidを取得するメソッド
     * @param Collection $select_memo_images
     * @return array
     */
    public static function getMemoImagesId(Collection $select_memo_images): array
    {
        // メモにリレーションされた画像のidを、配列に追加
        $memo_in_images_id = [];
        foreach ($select_memo_images as $memo_relation_image) {
            $memo_in_images_id[] = $memo_relation_image->id;
        }
        return $memo_in_images_id;
    }

    /**
     * 画像をリサイズして、Laravelのフォルダ内に保存するメソッド。
     * @param UploadedFile $image_file
     * @param ImageManager $manager
     * @return string
     */
    public static function afterResizingImage(UploadedFile $image_file, ImageManager $manager): string
    {
        // ランダムなファイル名の生成
        $rnd_file_name = uniqid(rand() . '_');
        // ランダムなファイル名と拡張子を結合
        $only_one_file_name = $rnd_file_name . '.' . 'jpeg';
        // 実際のリサイズ
        $resize_image = $manager->read($image_file)
            ->resize(720, 480)
            ->toJpeg();
        // 保存場所とファイル名を指定して、Laravel内に保存
        Storage::put('public/' . $only_one_file_name, $resize_image);
        return $only_one_file_name;
    }

    /**
     * Storageフォルダ内の画像ファイルを削除するメソッド。
     * @param string $image_filename
     * @return void
     */
    public static function deleteStorage(string $image_filename): void
    {
        // Storageフォルダ内の画像ファイルを削除
        $file_path = 'public/' . $image_filename;
        if (Storage::exists($file_path)) {
            Storage::delete($file_path);
        }
    }
}

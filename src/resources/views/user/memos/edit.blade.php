<x-app-layout>
    <div class="px-2 py-2 bg-slate-200">
        <section class="min-h-[45vh] text-gray-600 border border-gray-400 rounded-lg bg-white overflow-hidden">
            {{-- メモの編集ページのタイトル --}}
            <h1 class="heading heading_bg">メモ編集</h1>
            {{-- 選択したメモを編集するエリア --}}
            <div class="p-3">
                <form action="{{ route('user.update') }}" method="post">
                    @csrf
                    @method('patch')
                    {{-- 共有中のメモの目印 --}}
                    @if ($select_memo->status)
                        <div class="mark_bg"><p class="mark">{{ $select_memo->status }}</p></div>
                    @endif
                    {{-- 選択したメモのタイトルを表示 --}}
                    <div class="mb-5">
                        <h2 class="sub_heading mb-1">タイトル</h2>
                        <input class="w-60 rounded" type="text" name="title" value="{{ $select_memo->title }}"
                               placeholder="ここにタイトルを入力"/>
                        {{-- エラーメッセージ（メモのタイトル）--}}
                        <x-input-error class="mt-2" :messages="$errors->get('title')"/>
                    </div>
                    {{-- 選択したメモの内容の表示 --}}
                    <div class="mb-5">
                        <h2 class="sub_heading mb-1">内容</h2>
                        <textarea class="w-full rounded" name="content" rows="7"
                                  placeholder="ここにメモを入力">{{ $select_memo->content }}</textarea>
                        {{-- エラーメッセージ（メモの内容）--}}
                        <x-input-error class="mt-2" :messages="$errors->get('content')"/>
                    </div>
                    {{-- 選択したメモに紐づいた既存タグを表示 --}}
                    <div class="mb-10">
                        <h2 class="sub_heading mb-1">既存タグの選択</h2>
                        @foreach ($all_tags as $tag)
                            <div class="inline mr-3 hover:font-semibold">
                                <input class="mb-1 rounded" type="checkbox" name="tags[]" id="{{ $tag->id }}"
                                       value="{{ $tag->id }}" {{ in_array($tag->id, $get_memo_tags_id) ? 'checked' : '' }} />
                                <label for="{{ $tag->id }}">{{ $tag->name }}</label>
                            </div>
                        @endforeach
                    </div>
                    {{-- 新規タグ入力 --}}
                    <div class="mb-10">
                        <h2 class="sub_heading mb-1">新規タグの追加</h2>
                        <div class="mr-5">
                            <input class="w-60 rounded" type="text" name="new_tag" placeholder="ここに新規タグを入力"/>
                        </div>
                        {{-- エラーメッセージ（新規タグ） --}}
                        <x-input-error class="mt-2" :messages="$errors->get('new_tag')"/>
                    </div>
                    {{-- 選択したメモに紐づいた画像の表示 --}}
                    <div class="mb-10">
                        <h2 class="sub_heading mb-1">画像の選択</h2>
                        {{-- モーダルウィンドウ --}}
                        <x-common.list-select-image :allImages='$all_images' :getMemoImagesId="$get_memo_images_id"/>
                    </div>
                    {{-- 選択されているメモのidを取得 --}}
                    <input type="hidden" name="memoId" value="{{ $select_memo->id }}">
                    {{-- メモの更新ボタン --}}
                    <div class="mb-5">
                        <button class="btn bg-blue-800 hover:bg-blue-700" type="submit">更新する</button>
                    </div>
                </form>
                {{-- 戻るボタン --}}
                <div class="mb-2 flex justify-end">
                    <button class="btn bg-gray-800 hover:bg-gray-700"
                            onclick="location.href='{{ route('user.index') }}'">
                        戻る
                    </button>
                </div>
            </div>
        </section>
    </div>
    <script>
        'use strict'
        // 最初に一度だけ発生するイベント（メモに紐づいた画像を表示する為）
        document.addEventListener('DOMContentLoaded', function () {
            // Laravelから送られてくるデータをBladeで設定、メモに紐づいた画像を取得
            @php
                $selectedImages = json_encode($get_memo_images);
            @endphp
            // Laravelから送られてきたデータをJavaScriptに渡す
            const selectedImagesData = JSON.parse('{!! addslashes($selectedImages) !!}');

            selectedImagesData.forEach(data => {
                // 選択されていた画像のデータ
                const imageId = data.id;
                const imageFile = data.filename;
                const imagePath = document.querySelector('.image').dataset.path;
                selectedImages.push({
                    id: imageId,
                    file: imageFile,
                    path: imagePath
                });
                // サムネイルエリアに選択した画像を表示
                const thumbnailImage = document.createElement('img');
                thumbnailImage.src = imagePath + '/' + imageFile;
                thumbnailImage.classList.add('mr-2', 'mb-2', 'border', 'rounded-md', 'p-1', 'w-[22.7%]', 'sm:w-[23%]');
                thumbnailArea.appendChild(thumbnailImage);
            });
        });

        // 画像の上限数の設定
        const maxCount = 4;
        // チェックボックスをクラス名で取得
        const checkBoxClassName = "imageCheckbox";

        // チェックボックスが変更されたときに呼び出されるメソッド
        function handleCheckboxChange() {
            const checkBoxes = document.getElementsByClassName(checkBoxClassName);
            let checked_count = 0;

            // 画像の枚数を、チェックする
            for (let i = 0; i < checkBoxes.length; i++) {
                if (checkBoxes[i].checked) {
                    checked_count++;
                }
            }
            // 画像の上限枚数に、達したら選択をキャンセル
            if (checked_count > maxCount) {
                alert("画像は " + maxCount + " 枚までにしてください。");
                this.checked = false;
            }
        }

        // チェックボックスの変更イベントにメソッドを紐付ける
        const checkBoxes = document.getElementsByClassName(checkBoxClassName);
        for (let i = 0; i < checkBoxes.length; i++) {
            checkBoxes[i].addEventListener('change', handleCheckboxChange);
        }

        // 画像要素を取得
        const images = document.querySelectorAll('.image');
        // サムネイルコンテナ要素を取得
        const thumbnailArea = document.getElementById('thumbnail-area');
        // ユーザーが選択した画像を保持する配列
        const selectedImages = [];

        //各画像にクリックイベントリスナーを追加
        images.forEach(image => {
            image.addEventListener('click', function (e) {
                // クリックされた画像のデータ属性を取得
                const imageId = Number(e.target.dataset.id);
                const imageFile = e.target.dataset.file;
                const imagePath = e.target.dataset.path;

                // 画像がすでに選択されているか確認
                const isSelecte = selectedImages.some(img => img.id === imageId);
                if (!isSelecte && selectedImages.length < maxCount) {
                    // 上限数に達していない場合、ユーザーが選択した画像を配列に追加
                    selectedImages.push({
                        id: imageId,
                        file: imageFile,
                        path: imagePath
                    });
                    // サムネイルエリアに選択した画像を表示
                    const thumbnailImage = document.createElement('img');
                    thumbnailImage.src = imagePath + '/' + imageFile;
                    thumbnailImage.classList.add('mr-2', 'mb-2', 'border', 'rounded-md', 'p-1', 'w-[22.7%]', 'sm:w-[23%]');
                    thumbnailArea.appendChild(thumbnailImage);
                } else if (isSelecte) {
                    // すでに選択されている場合、配列から削除
                    const imageArrayExistsDelete = selectedImages.findIndex(img => img.id === imageId);
                    selectedImages.splice(imageArrayExistsDelete, 1);
                    // 続けて、サムネイルエリアに選択されている画像を削除
                    const thumbnailDelete = thumbnailArea.querySelector(
                        `[src="${imagePath}/${imageFile}"]`);
                    if (thumbnailDelete) {
                        thumbnailArea.removeChild(thumbnailDelete);
                    }
                }
            });
        });
    </script>
</x-app-layout>

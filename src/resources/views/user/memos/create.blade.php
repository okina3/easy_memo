<x-app-layout>
    <div class="px-2 py-2 bg-slate-200">
        <section class="min-h-[45vh] text-gray-600 border border-gray-400 rounded-lg bg-white overflow-hidden">
            {{-- メモの新規作成ページのタイトル --}}
            <h1 class="heading heading_bg">新規メモ作成</h1>
            {{-- メモを新規作成するエリア --}}
            <div class="p-3">
                <form action="{{ route('user.store') }}" method="post">
                    @csrf
                    {{-- メモのタイトル入力 --}}
                    <div class="mb-5">
                        <h2 class="sub_heading mb-1">タイトル</h2>
                        <input class="w-60 rounded" type="text" name="title" value="{{ old('title') }}"
                               placeholder="ここにタイトルを入力"/>
                        {{-- エラーメッセージ（メモのタイトル） --}}
                        <x-input-error class="mt-2" :messages="$errors->get('title')"/>
                    </div>
                    {{-- メモの内容入力 --}}
                    <div class="mb-5">
                        <h2 class="sub_heading mb-1">内容</h2>
                        <textarea class="w-full rounded" name="content" rows="7"
                                  placeholder="ここにメモを入力">{{ old('content') }}</textarea>
                        {{-- エラーメッセージ（メモの内容） --}}
                        <x-input-error class="mt-2" :messages="$errors->get('content')"/>
                    </div>
                    {{-- 既存タグの選択 --}}
                    <div class="mb-10">
                        <h2 class="sub_heading mb-1">既存タグの選択</h2>
                        @foreach ($all_tags as $tag)
                            <div class="inline mr-3 hover:font-semibold">
                                <input class="mb-1 rounded" type="checkbox" name="tags[]" id="{{ $tag->id }}"
                                       value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }} />
                                <label for="{{ $tag->id }}">{{ $tag->name }}</label>
                            </div>
                        @endforeach
                    </div>
                    {{-- 新規タグ入力 --}}
                    <div class="mb-10">
                        <h2 class="sub_heading mb-1">新規タグの追加</h2>
                        <input class="w-60 rounded" type="text" name="new_tag" value="{{ old('new_tag') }}"
                               placeholder="ここに新規タグを入力"/>
                        {{-- エラーメッセージ（新規タグ） --}}
                        <x-input-error class="mt-2" :messages="$errors->get('new_tag')"/>
                    </div>
                    {{-- 画像の選択 --}}
                    <div class="mb-10">
                        <h2 class="sub_heading">画像の選択</h2>
                        {{-- モーダルウィンドウ --}}
                        <x-common.list-select-image :allImages='$all_images'/>
                    </div>
                    {{-- メモの保存ボタン --}}
                    <div class="mb-5">
                        <button class="btn bg-blue-800 hover:bg-blue-700" type="submit">保存する</button>
                    </div>
                </form>
                {{-- 戻るボタン --}}
                <div class="flex justify-end">
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

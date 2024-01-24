<x-app-layout>
    <section class="min-h-[45vh] text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
        {{-- メモの新規作成ページのタイトル --}}
        <div class="px-3 py-2 flex justify-between items-center border-b border-gray-400 bg-gray-200">
            <h1 class="py-1 text-xl font-semibold">新規メモ作成</h1>
        </div>
        {{-- メモを新規作成するエリア --}}
        <div class="p-3">
            <x-common.flash-message status="session('status')"/>
            <form action="{{ route('user.store') }}" method="post">
                @csrf
                {{-- メモのタイトル入力 --}}
                <div class="mb-5">
                    <h1 class="mb-1 text-lg font-semibold">タイトル</h1>
                    <input type="text" class="w-60 rounded" name="title" value="{{ old('title') }}"
                           placeholder="ここにタイトルを入力"/>
                    {{-- エラーメッセージ（メモのタイトル） --}}
                    <x-input-error :messages="$errors->get('title')" class="mt-2"/>
                </div>
                {{-- メモの内容入力 --}}
                <div class="mb-5">
                    <h1 class="mb-1 text-lg font-semibold">内容</h1>
                    <textarea class="w-full rounded" name="content" rows="7"
                              placeholder="ここにメモを入力">{{ old('content') }}</textarea>
                    {{-- エラーメッセージ（メモの内容） --}}
                    <x-input-error :messages="$errors->get('content')" class="mt-2"/>
                </div>
                {{-- 既存タグの選択 --}}
                <div class="mb-10">
                    <h1 class="mb-1 text-lg font-semibold">既存タグの選択</h1>
                    @foreach ($all_tags as $tag)
                        <div class="inline mr-3 hover:font-semibold">
                            <input type="checkbox" class="mb-1 rounded" name="tags[]" id="{{ $tag->id }}"
                                   value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }} />
                            <label for="{{ $tag->id }}">{{ $tag->name }}</label>
                        </div>
                    @endforeach
                </div>
                {{-- 新規タグ入力 --}}
                <div class="mb-10">
                    <h1 class="mb-1 text-lg font-semibold">新規タグの追加</h1>
                    <input type="text" class="w-60 rounded" name="new_tag" value="{{ old('new_tag') }}"
                           placeholder="ここに新規タグを入力"/>
                    {{-- エラーメッセージ（新規タグ） --}}
                    <x-input-error :messages="$errors->get('new_tag')" class="mt-2"/>
                </div>
                {{-- 画像の選択 --}}
                <div class="mb-10">
                    <h1 class="text-lg font-semibold">画像の選択</h1>
                    {{-- モーダルウィンドウ --}}
                    <x-common.list-select-image :allImages='$all_images'/>
                </div>
                {{-- メモの保存ボタン --}}
                <div class="mb-5">
                    <button type="submit" class="py-1 px-4 text-white rounded bg-blue-800 hover:bg-blue-700">
                        保存する
                    </button>
                </div>
            </form>
            {{-- 戻るボタン --}}
            <div class="flex justify-end">
                <button onclick="location.href='{{ route('user.index') }}'"
                        class="py-1 px-3 text-white rounded bg-gray-800 hover:bg-gray-700">
                    戻る
                </button>
            </div>
        </div>
    </section>
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
                    thumbnailImage.classList.add('mr-2', 'mb-2', 'border', 'rounded-md', 'p-1', 'w-1/5');
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

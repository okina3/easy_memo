<x-app-layout>
    <section class="min-h-[45vh] text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
        <div class="px-3 py-2 border-b border-gray-400 bg-gray-200">
            <h1 class="py-1 text-xl font-semibold">メモ編集</h1>
        </div>
        <div class="p-3">
            <form action="{{ route('user.update') }}" method="post">
                @csrf
                @method('patch')
                {{-- 選択されているメモのidを取得 --}}
                <input type="hidden" name="memoId" value="{{ $choice_memo->id }}">
                {{-- 共有中のメモの目印 --}}
                @if ($choice_memo->status)
                    <div class="mb-1 inline-block rounded-xl bg-cyan-600">
                        <div class="py-0.5 px-2 text-sm text-white font-semibold ">
                            {{ $choice_memo->status }}
                        </div>
                    </div>
                @endif
                {{-- タイトル --}}
                <div class="mb-5">
                    <h1 class="mb-1 text-lg font-semibold">タイトル</h1>
                    <input type="text" class="w-60 rounded" name="title" value="{{ $choice_memo->title }}"
                           placeholder="ここにタイトルを入力"/>
                    {{-- 新規タグのエラーメッセージ --}}
                    <x-input-error :messages="$errors->get('title')" class="mt-2"/>
                </div>
                {{-- 選択したメモの内容表示エリア --}}
                <div class="mb-5">
                    <h1 class="mb-1 text-lg font-semibold">内容</h1>
                    <textarea class="w-full rounded" name="content" rows="7"
                              placeholder="ここにメモを入力">{{ $choice_memo->content }}</textarea>
                    {{-- メモの内容エラーメッセージ --}}
                    <x-input-error :messages="$errors->get('content')" class="mt-2"/>
                </div>

                {{-- 既存タグの選択エリア --}}
                <div class="mb-10">
                    <h1 class="mb-1 text-lg font-semibold">既存タグの選択</h1>
                    @foreach ($all_tags as $tag)
                        <div class="inline mr-3 hover:font-semibold">
                            <input type="checkbox" class="mb-1 rounded" name="tags[]" id="{{ $tag->id }}"
                                   value="{{ $tag->id }}" {{ in_array($tag->id, $memo_in_tags) ? 'checked' : '' }} />
                            <label for="{{ $tag->id }}">{{ $tag->name }}</label>
                        </div>
                    @endforeach
                </div>

                {{-- 新規タグ入力エリア --}}
                <div class="mb-10">
                    <h1 class="mb-1 text-lg font-semibold">新規タグの追加</h1>
                    <div class="mr-5">
                        <input type="text" class="w-60 rounded" name="new_tag" placeholder="ここに新規タグを入力"/>
                    </div>
                    {{-- 新規タグのエラーメッセージ --}}
                    <x-input-error :messages="$errors->get('new_tag')" class="mt-2"/>
                </div>
                {{-- 選択画像の表示 --}}
                <div class="mb-10">
                    <h1 class="mb-1 text-lg font-semibold">画像の選択</h1>
                    {{-- モーダルウィンドウ --}}
                    <x-common.list-select-image :allImages='$all_images' :memoInImagesId="$memo_in_images_id"/>
                </div>
                <div class="mb-5">
                    <button type="submit" class="py-1 px-4 text-white rounded bg-blue-800 hover:bg-blue-700">
                        更新する
                    </button>
                </div>
            </form>
            <div class="mb-2 flex justify-end">
                <button onclick="location.href='{{ route('user.index') }}'"
                        class="py-1 px-3 text-white rounded bg-gray-800 hover:bg-gray-700">
                    戻る
                </button>
            </div>
        </div>
    </section>
    <script>
        'use strict'
        // 最初に一度だけ発生するイベント
        document.addEventListener('DOMContentLoaded', function () {
            // Laravelから送られてくるデータをBladeで設定、メモに紐づいた画像を取得
            @php
                $selectedImages = json_encode($memo_in_images);
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
                thumbnailImage.classList.add('mr-2', 'mb-2', 'border', 'rounded-md', 'p-1', 'w-1/5');
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

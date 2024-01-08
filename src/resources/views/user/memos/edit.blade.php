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
        //   {{-- @vite() --}}
        // 最初に一度だけ発生するイベント
        document.addEventListener('DOMContentLoaded', function () {
            // Laravelから送られてくるデータをBladeで設定、メモに紐づいた画像を取得
            @php
                $selectedImages = json_encode($memo_in_images);
            @endphp
            // Laravelから送られてきたデータをJavaScriptに渡す
            const SELECTED_IMAGES_DATA = JSON.parse('{!! addslashes($selectedImages) !!}');

            SELECTED_IMAGES_DATA.forEach(data => {
                // 選択されていた画像のデータ
                const IMAGE_ID = data.id;
                const IMAGE_FILE = data.filename;
                const IMAGE_PATH = document.querySelector('.image').dataset.path;
                SELECTED_IMAGES.push({
                    id: IMAGE_ID,
                    file: IMAGE_FILE,
                    path: IMAGE_PATH
                });
                // サムネイルエリアに選択した画像を表示
                const THUMBNAIL_IMAGE = document.createElement('img');
                THUMBNAIL_IMAGE.src = IMAGE_PATH + '/' + IMAGE_FILE;
                THUMBNAIL_IMAGE.classList.add('mr-2', 'mb-2', 'border', 'rounded-md', 'p-1', 'w-1/5');
                THUMBNAIL_AREA.appendChild(THUMBNAIL_IMAGE);
            });
        });


        // 画像の上限数の設定
        const MAX_COUNT = 4;
        // チェックボックスをクラス名で取得
        const CHECK_BOX_CLASS_NAME = "imageCheckbox";

        // チェックボックスが変更されたときに呼び出されるメソッド
        function handleCheckboxChange() {
            const CHECK_BOXES = document.getElementsByClassName(CHECK_BOX_CLASS_NAME);
            let checked_count = 0;

            // 画像の枚数を、チェックする
            for (let i = 0; i < CHECK_BOXES.length; i++) {
                if (CHECK_BOXES[i].checked) {
                    checked_count++;
                }
            }
            // 画像の上限枚数に、達したら選択をキャンセル
            if (checked_count > MAX_COUNT) {
                alert("画像は " + MAX_COUNT + " 枚までにしてください。");
                this.checked = false;
            }
        }

        // チェックボックスの変更イベントにメソッドを紐付ける
        const CHECK_BOXES = document.getElementsByClassName(CHECK_BOX_CLASS_NAME);
        for (let i = 0; i < CHECK_BOXES.length; i++) {
            CHECK_BOXES[i].addEventListener('change', handleCheckboxChange);
        }


        // 画像要素を取得
        const IMAGES = document.querySelectorAll('.image');
        // サムネイルコンテナ要素を取得
        const THUMBNAIL_AREA = document.getElementById('thumbnail-area');
        // ユーザーが選択した画像を保持する配列
        const SELECTED_IMAGES = [];

        //各画像にクリックイベントリスナーを追加
        IMAGES.forEach(image => {
            image.addEventListener('click', function (e) {
                // クリックされた画像のデータ属性を取得
                const IMAGE_ID = Number(e.target.dataset.id);
                const IMAGE_FILE = e.target.dataset.file;
                const IMAGE_PATH = e.target.dataset.path;

                // 画像がすでに選択されているか確認
                const IS_SELECTED = SELECTED_IMAGES.some(img => img.id === IMAGE_ID);

                if (!IS_SELECTED && SELECTED_IMAGES.length < MAX_COUNT) {
                    // 上限数に達していない場合、ユーザーが選択した画像を配列に追加
                    SELECTED_IMAGES.push({
                        id: IMAGE_ID,
                        file: IMAGE_FILE,
                        path: IMAGE_PATH
                    });
                    // サムネイルエリアに選択した画像を表示
                    const THUMBNAIL_IMAGE = document.createElement('img');
                    THUMBNAIL_IMAGE.src = IMAGE_PATH + '/' + IMAGE_FILE;
                    THUMBNAIL_IMAGE.classList.add('mr-2', 'mb-2', 'border', 'rounded-md', 'p-1', 'w-1/5');
                    THUMBNAIL_AREA.appendChild(THUMBNAIL_IMAGE);
                } else if (IS_SELECTED) {
                    // すでに選択されている場合、配列から削除
                    const IMAGE_ARRAY_EXISTS_DELETE = SELECTED_IMAGES.findIndex(img => img.id === IMAGE_ID);
                    SELECTED_IMAGES.splice(IMAGE_ARRAY_EXISTS_DELETE, 1);

                    // 続けて、サムネイルエリアに選択されている画像を削除
                    const THUMBNAIL_DELETE = THUMBNAIL_AREA.querySelector(
                        `[src="${IMAGE_PATH}/${IMAGE_FILE}"]`);
                    if (THUMBNAIL_DELETE) {
                        THUMBNAIL_AREA.removeChild(THUMBNAIL_DELETE);
                    }
                }
            });
        });
    </script>
</x-app-layout>

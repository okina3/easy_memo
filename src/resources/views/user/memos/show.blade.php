<x-app-layout>
    <section class="min-h-[45vh] text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
        {{-- メモの詳細ページのタイトル --}}
        <h1 class="heading heading_bg">メモ詳細</h1>
        {{-- 選択したメモの詳細を表示するエリア --}}
        <div class="p-3">
            {{-- メモの共有設定を表示するエリア --}}
            <x-common.memo-share-status :selectMemoId='$select_memo->id' :sharedUsers='$shared_users'/>
            {{-- メモの詳細を表示エリア --}}
            <div class="mb-3">
                {{-- 共有中のメモの目印 --}}
                @if ($select_memo->status)
                    <div class="mark_bg"><p class="mark">{{ $select_memo->status }}</p></div>
                @endif
                {{-- 選択したメモのタイトルを表示 --}}
                <div class="mb-5">
                    <h2 class="sub_heading mb-1">タイトル</h2>
                    <p class="p-2 border border-gray-500 rounded bg-white">{{ $select_memo->title }}</p>
                </div>
                {{-- 選択したメモの内容を表示 --}}
                <div class="mb-5">
                    <h2 class="sub_heading mb-1">内容</h2>
                    <textarea class="w-full rounded" name="content" rows="7"
                              disabled>{{ $select_memo->content }}</textarea>
                </div>
                {{-- 選択したメモのタグを表示 --}}
                <div class="mb-10">
                    <h2 class="sub_heading mb-1">タグ</h2>
                    @foreach ($get_memo_tags as $tag)
                        <div class="inline mr-3">
                            <input class="mb-1 rounded" type="checkbox" checked disabled/>
                            {{ $tag }}
                        </div>
                    @endforeach
                </div>
                {{-- 選択したメモの画像の表示 --}}
                <div class="mb-10">
                    <h2 class="sub_heading mb-1">登録画像</h2>
                    {{-- モーダルウィンドウ --}}
                    <x-common.big-select-image :getMemoImages='$get_memo_images'/>
                </div>
                {{-- 戻るボタン --}}
                <div class="mb-2 flex justify-end">
                    <button onclick="location.href='{{ route('user.index') }}'"
                            class="btn bg-gray-800 hover:bg-gray-700">
                        戻る
                    </button>
                </div>
            </div>
        </div>
    </section>
    <script>
        'use strict'
        // 共有情報を見る為の、アコーディオンの為の記述
        document.addEventListener('DOMContentLoaded', function () {
            // アコーディオンのボタンと、共有設定の内容を取得
            const INFORMATION = document.getElementById('shared-information');
            const BUTTON = document.getElementById('shared-button');
            // ボタンがクリックされたときの処理
            BUTTON.addEventListener('click', function () {
                // 共有設定の内容の表示/非表示を切り替え
                INFORMATION.classList.toggle('active');
            });
        });
    </script>
</x-app-layout>

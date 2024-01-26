<x-app-layout>
    <section class="min-h-[45vh] text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
        {{-- メモの詳細ページのタイトル --}}
        <div class="heading_bg"><h1 class="heading">メモ詳細</h1></div>
        {{-- 選択したメモの詳細を表示するエリア --}}
        <div class="p-3">
            {{-- メモの共有設定を表示するエリア --}}
            <x-common.memo-share-status :choiceMemoId='$choice_memo->id' :sharedUsers='$shared_users'/>
            {{-- メモの詳細を表示エリア --}}
            <div class="mb-3">
                {{-- 共有中のメモの目印 --}}
                @if ($choice_memo->status)
                    <div class="mark_bg">
                        <div class="mark">{{ $choice_memo->status }}</div>
                    </div>
                @endif
                {{-- 選択したメモのタイトルを表示 --}}
                <div class="mb-5">
                    <h1 class="sub_heading mb-1">タイトル</h1>
                    <div class="p-2 border border-gray-500 rounded bg-white">
                        {{ $choice_memo->title }}
                    </div>
                </div>
                {{-- 選択したメモの内容を表示 --}}
                <div class="mb-5">
                    <h1 class="sub_heading mb-1">内容</h1>
                    <textarea class="w-full rounded" name="content" rows="7" placeholder="ここにメモを入力"
                              disabled>{{ $choice_memo->content }}</textarea>
                </div>
                {{-- 選択したメモのタグを表示 --}}
                <div class="mb-10">
                    <h1 class="sub_heading mb-1">タグ</h1>
                    @foreach ($memo_in_tags as $tag)
                        <div class="inline mr-3">
                            <input class="mb-1 rounded" type="checkbox" checked disabled/>
                            {{ $tag }}
                        </div>
                    @endforeach
                </div>
                {{-- 選択したメモの画像の表示 --}}
                <div class="mb-10">
                    <h1 class="sub_heading mb-1">登録画像</h1>
                    {{-- モーダルウィンドウ --}}
                    <x-common.big-select-image :memoInImages='$memo_in_images'/>
                </div>
                {{-- 戻るボタン --}}
                <div class="mb-2 flex justify-end">
                    <button onclick="location.href='{{ route('user.index') }}'"
                            class="btn mr-1 bg-gray-800 hover:bg-gray-700">
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

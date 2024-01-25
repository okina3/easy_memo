<x-app-layout>
    <section class="min-h-[45vh] text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
        {{-- メモの詳細ページのタイトル --}}
        <div class="px-3 py-2.5 border-b border-gray-400 bg-gray-200">
            <h1 class="text-xl font-semibold">メモ詳細</h1>
        </div>
        {{-- 選択したメモの詳細を表示するエリア --}}
        <div class="p-3">
            {{-- メモの共有設定を表示するエリア --}}
            <x-common.memo-share-status :choiceMemoId='$choice_memo->id' :sharedUsers='$shared_users'/>
            {{-- メモの詳細を表示エリア --}}
            <div class="mb-3">
                {{-- 共有中のメモの目印 --}}
                @if ($choice_memo->status)
                    <div class="mb-1 inline-block rounded-xl bg-cyan-600">
                        <div class="py-0.5 px-2 text-sm text-white font-semibold ">
                            {{ $choice_memo->status }}
                        </div>
                    </div>
                @endif
                {{-- 選択したメモのタイトルを表示 --}}
                <div class="mb-5">
                    <h1 class="mb-1 text-lg font-semibold">タイトル</h1>
                    <div class="p-2 border border-gray-500 rounded bg-white">
                        {{ $choice_memo->title }}
                    </div>
                </div>
                {{-- 選択したメモの内容を表示 --}}
                <div class="mb-5">
                    <h1 class="mb-1 text-lg font-semibold">内容</h1>
                    <textarea class="w-full rounded" name="content" rows="7" placeholder="ここにメモを入力"
                              disabled>{{ $choice_memo->content }}</textarea>
                </div>
                {{-- 選択したメモのタグを表示 --}}
                <div class="mb-10">
                    <h1 class="mb-1 text-lg font-semibold">タグ</h1>
                    @foreach ($memo_in_tags as $tag)
                        <div class="inline mr-3">
                            <input type="checkbox" class="mb-1 rounded" checked disabled/>
                            {{ $tag }}
                        </div>
                    @endforeach
                </div>
                {{-- 選択したメモの画像の表示 --}}
                <div class="mb-10">
                    <h1 class="mb-1 text-lg font-semibold">登録画像</h1>
                    {{-- モーダルウィンドウ --}}
                    <x-common.big-select-image :memoInImages='$memo_in_images'/>
                </div>
                {{-- 戻るボタン --}}
                <div class="mb-2 flex justify-end">
                    <button onclick="location.href='{{ route('user.index') }}'"
                            class="mr-1 py-1 px-3 text-white rounded bg-gray-800 hover:bg-gray-700">
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

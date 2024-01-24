<x-app-layout>
    <section class="min-h-[45vh] text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
        {{-- メモの詳細ページのタイトル --}}
        <div class="px-3 py-1 font-semibold border-b border-gray-400 bg-gray-200">
            <h1 class="text-xl">メモ詳細</h1>
        </div>
        {{-- 選択したメモの詳細を表示するエリア --}}
        <div class="p-3">
            {{-- メモの共有状態を表示するエリア（アコーディオン） --}}
            <div class="mb-3">
                <div id="shared-information">
                    {{-- アコーディオンの開閉ボタン --}}
                    <div id="shared-button">
                        <button type="button" class="accordion-button">
                            メモの共有設定
                        </button>
                    </div>
                    {{-- エラーメッセージ（共有設定） --}}
                    <div class="mt-2">
                        <x-input-error :messages="$errors->get('share_user_start')"/>
                        <x-input-error :messages="$errors->get('share_user_end')"/>
                    </div>
                    {{-- メモの共有の設定エリア --}}
                    <div class="accordion-body">
                        <div class="border-b-4 border-gray-700">
                            {{-- メモの共有を開始するエリア --}}
                            <form action="{{ route('user.share-setting.store') }}" method="post">
                                @csrf
                                {{-- 選択されているメモのidを取得 --}}
                                <input type="hidden" name="memoId" value="{{ $choice_memo->id }}">
                                <div class="mb-3 pb-5 border-b border-gray-400">
                                    {{-- 共有設定のタイトル --}}
                                    <h1 class="mb-1 text-lg font-semibold">このメモを共有する</h1>
                                    {{-- ユーザーのメールアドレスを入力 --}}
                                    <div class="mb-3">
                                        <p class="text-sm">
                                            共有したいユーザーのメールアドレスを入力してください。
                                        </p>
                                        <input type="text" class="w-60 rounded" name="share_user_start"
                                               placeholder="メールアドレスを入力">
                                    </div>
                                    {{-- 編集権限のボタン --}}
                                    <div class="mb-3">
                                        <p class="text-sm">
                                            共有したユーザーに、メモの編集を許可しますか？
                                        </p>
                                        <input type="radio" class="ml-2" name="edit_access" id="yes_access" value=1
                                            {{ old('edit_access') == 1 ? 'checked' : '' }} />
                                        <label for="yes_access">はい</label>
                                        <input type="radio" class="ml-10" name="edit_access" id="no_access" value=0
                                            {{ old('edit_access') == 0 ? 'checked' : '' }} />
                                        <label for="no_access">いいえ</label>
                                    </div>
                                    {{-- メモを共有するボタン --}}
                                    <button type="submit"
                                            class="py-1 px-3 block text-white rounded bg-cyan-600 hover:bg-cyan-700">
                                        共有する
                                    </button>
                                </div>
                            </form>
                            {{-- メモを共有中のユーザーの情報を表示するエリア --}}
                            <div class="mb-3 pb-4 border-b border-gray-400">
                                {{-- 共有設定のタイトル --}}
                                <h1 class="mb-1 text-lg font-semibold">
                                    共有中のユーザー
                                </h1>
                                {{-- 共有中のユーザーの情報を表示 --}}
                                <ul
                                    class="p-2 max-h-[25vh] border border-gray-400 rounded bg-white overflow-y-scroll overscroll-none">
                                    @foreach ($shared_users as $shared_user)
                                        <li class="mb-1 border-b border-gray-400">
                                            {{-- 共有中のユーザーの名前 --}}
                                            <div>
                                                <span>ユーザー名・・・・・</span>
                                                <span class="font-semibold">{{ $shared_user->name }}</span>
                                            </div>
                                            {{-- 共有中のユーザーのメールアドレス --}}
                                            <div>
                                                <span>メールアドレス・・・</span>
                                                <span class="font-semibold">{{ $shared_user->email }}</span>
                                            </div>
                                            {{-- 共有中のメモのアクセス許可の判定 --}}
                                            <div>
                                                <span>アクセス許可・・・・</span>
                                                @if ($shared_user->access === 1)
                                                    <span class="font-semibold">
                                                        詳細、<span class=" text-blue-800">編集</span>も可
                                                    </span>
                                                @endif
                                                @if ($shared_user->access === 0)
                                                    <span class="font-semibold">詳細のみ</span>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            {{-- メモの共有を停止するエリア --}}
                            <form action="{{ route('user.share-setting.destroy') }}" method="post">
                                @csrf
                                @method('delete')
                                {{-- 選択されているメモのidを取得 --}}
                                <input type="hidden" name="memoId" value="{{ $choice_memo->id }}">
                                <div class="mb-3 pb-5">
                                    {{-- 共有設定のタイトル --}}
                                    <h1 class="mb-1 text-lg font-semibold">このメモの共有を停止する</h1>
                                    {{-- ユーザーのメールアドレスを入力 --}}
                                    <p class="text-sm">
                                        共有したい停止したいユーザーのメールアドレスを入力してください。
                                    </p>
                                    <input type="text" class="mb-2 w-60 rounded" name="share_user_end"
                                           placeholder="メールアドレスを入力">
                                    {{-- メモの共有を停止するボタン --}}
                                    <button type="submit"
                                            class="py-1 px-3 block text-white rounded bg-cyan-600 hover:bg-cyan-700">
                                        共有停止
                                    </button>
                                    {{-- コメント --}}
                                    <p class="text-sm mt-2">
                                        ※ このメモの全てのユーザーの共有を停止したい場合は、メモを削除してください。
                                    </p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

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

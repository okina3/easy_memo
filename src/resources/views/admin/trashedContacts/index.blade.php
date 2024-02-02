<x-app-layout>
    {{-- フラッシュメッセージ --}}
    <x-common.flash-message status="session('status')"/>
    <div class="max-w-screen-lg mx-auto">
        {{-- 検索の表示エリア --}}
        <section class="mb-5 px-3 py-2 text-gray-600 border border-gray-400 rounded-lg bg-gray-200">
            <form action="{{ route('admin.contact.index') }}" method="get">
                <div class="flex space-x-2 items-center">
                    <div class="heading">キーワードから検索・・・・・</div>
                    {{-- キーワードを入力 --}}
                    <input class="py-2 w-60 border border-gray-500 rounded-lg" name="keyword"
                            placeholder="キーワードを入力">
                    {{-- 検索するボタン --}}
                    <button class="btn bg-blue-800 hover:bg-blue-700">検索する</button>
                </div>
            </form>
            {{-- コメント --}}
            <p class="text-sm mt-2">※ キーワードは、件名、問い合わせ内容の、両方から検索します。</p>
        </section>
        {{-- ユーザーからの問い合わせ一覧の表示エリア --}}
        <section class="text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
            {{-- タイトル --}}
            <h1 class="heading heading_bg">削除済みユーザーからの問い合わせ一覧</h1>
            {{-- ユーザーからの問い合わせ一覧 --}}
            <div class="p-2 h-[72vh] overflow-y-scroll overscroll-none">
                @foreach ($all_trashed_contacts as $contact)
                    <div class="mb-5 p-2 flex justify-between items-center border border-gray-400 rounded-lg">
                        <div class="mr-5 font-semibold truncate">
                            {{-- 件名 --}}
                            <p class="mb-1">
                                件名<span class="font-normal">・・・・・・・・</span>{{ $contact->subject }}
                            </p>
                            {{-- 問い合わせ内容 --}}
                            <p class="mb-1">
                                問い合わせ内容<span class="font-normal">・・・</span>{{ $contact->message }}
                            </p>
                        </div>
                        {{-- ボタンエリア --}}
                        <div class="flex justify-between">
                            {{-- 元に戻すボタン --}}
                            <form action="{{ route('admin.trashed-contact.undo') }}" method="post" class="mr-3">
                                @csrf
                                @method('patch')
                                {{-- 選択されている問い合わせのidを取得 --}}
                                <input type="hidden" name="contentId" value="{{ $contact->id }}">
                                <button class="btn w-24 bg-blue-800 hover:bg-blue-700" type="submit">元に戻す</button>
                            </form>
                            {{-- 完全削除ボタン --}}
                            <form onsubmit="return deleteCheck()" action="{{ route('admin.trashed-contact.destroy') }}"
                                  method="post">
                                @csrf
                                @method('delete')
                                {{-- 選択されている問い合わせのidを取得 --}}
                                <input type="hidden" name="contentId" value="{{ $contact->id }}">
                                <button class="btn w-24 bg-red-600 hover:bg-red-500" type="submit">完全削除</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
    <script>
        'use strict'

        // 削除のアラート
        function deleteCheck() {
            const RESULT = confirm('本当に削除してもいいですか?');
            if (!RESULT) alert("削除をキャンセルしました");
            return RESULT;
        }
    </script>
</x-app-layout>

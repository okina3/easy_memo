<x-app-layout>
    <x-common.flash-message status="session('status')"/>
    {{-- ユーザーの検索の表示エリア --}}
    <section class="mb-5 p-3 max-w-screen-lg mx-auto border border-gray-400 rounded-lg bg-gray-200">
        <form action="{{ route('admin.warning.index') }}" method="get">
            <div class="flex space-x-2 items-center">
                <div class="text-gray-600 text-xl font-semibold">
                    メールアドレスから検索・・・
                </div>
                {{-- メールアドレスを入力 --}}
                <input name="keyword" class="py-2 w-60 border border-gray-500 rounded-lg"
                       placeholder="メールアドレスを入力">
                {{-- 検索するボタン --}}
                <button class="py-1 px-3 rounded text-white bg-blue-800 hover:bg-blue-700">
                    検索する
                </button>
            </div>
        </form>
    </section>
    {{-- 警告されたユーザー一覧の表示エリア --}}
    <section class="max-w-screen-lg mx-auto text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
        {{-- タイトル --}}
        <div class="px-3 py-2 flex justify-between items-center border-b border-gray-400 bg-gray-200">
            <h1 class="text-xl font-semibold">警告したユーザー一覧</h1>
        </div>
        {{-- 警告されたユーザー一覧 --}}
        <div class="p-2 h-[75vh] overflow-y-scroll overscroll-none">
            @foreach ($warning_users_all as $warning_user)
                <div class="mb-5 p-2 flex justify-between items-center border border-slate-400 rounded-lg">
                    <div class="truncate">
                        {{-- ユーザー名前 --}}
                        <div class="mb-1">
                            <span class="font-semibold">ユーザー名</span>
                            <span>・・・・・・</span>
                            <span class="font-semibold border-b border-slate-400">
                                {{ $warning_user->name }}
                            </span>
                        </div>
                        {{-- ユーザーのメールアドレス --}}
                        <div class="mb-1">
                            <span class="font-semibold">メールアドレス</span>
                            <span>・・・・</span>
                            <span class="font-semibold border-b border-slate-400">
                                {{ $warning_user->email }}
                            </span>
                        </div>
                    </div>
                    {{-- ボタンエリア --}}
                    <div class="flex justify-between">
                        {{-- 元に戻すボタン --}}
                        <form action="{{ route('admin.warning.undo') }}" method="post" class="mr-3">
                            @csrf
                            @method('patch')
                            {{-- 選択されているメモのidを取得 --}}
                            <input type="hidden" name="userId" value="{{ $warning_user->id }}">
                            <button type="submit"
                                    class="py-1 px-2 w-24 text-white rounded bg-blue-800 hover:bg-blue-700">
                                利用再開
                            </button>
                        </form>
                        {{-- 完全削除ボタン --}}
                        <form onsubmit="return deleteCheck()" action="{{ route('admin.warning.destroy') }}"
                              method="post">
                            @csrf
                            @method('delete')
                            {{-- 選択されているメモのidを取得 --}}
                            <input type="hidden" name="userId" value="{{ $warning_user->id }}">
                            <button type="submit" class="py-1 px-2 w-24 text-white rounded bg-red-600 hover:bg-red-500">
                                完全削除
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
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

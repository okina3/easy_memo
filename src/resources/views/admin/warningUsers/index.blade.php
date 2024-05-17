<x-app-layout>
    <div class="max-w-screen-lg mx-auto">
    {{-- フラッシュメッセージ --}}
    <x-common.flash-message status="session('status')"/>
        {{-- ユーザーの検索の表示エリア --}}
        <section class="mb-5 px-3 py-2 text-gray-600 border border-gray-400 rounded-lg bg-gray-200">
            <form action="{{ route('admin.warning.index') }}" method="get">
                <div class="sm:flex items-center">
                    <div class="heading">メールアドレスから検索</div>
                    <div class="hidden sm:block">・・・</div>
                    {{-- メールアドレスを入力 --}}
                    <input class="py-2 w-60 border border-gray-500 rounded-lg" name="keyword"
                           placeholder="メールアドレスを入力">
                    {{-- 検索するボタン --}}
                    <button class="ml-2 btn bg-blue-800 hover:bg-blue-700">検索する</button>
                </div>
            </form>
        </section>
        {{-- 警告されたユーザー一覧の表示エリア --}}
        <section class="text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
            {{-- タイトル --}}
            <h1 class="heading heading_bg text-red-600">警告したユーザー一覧</h1>
            {{-- 警告されたユーザー一覧 --}}
            <div class="p-2 h-[74vh] overflow-y-scroll overscroll-none">
                @foreach ($all_warning_users as $warning_user)
                    <div class="mb-5 p-2 md:flex justify-between items-center border border-slate-400 rounded-lg">
                        <div class="md:w-[70%] mr-5 font-semibold">
                            {{-- ユーザー名前 --}}
                            <p class="mb-1 truncate">
                                ユーザー名<span class="font-normal">・・・・・・</span>
                                <span class="text-red-600 border-b border-slate-400">{{ $warning_user->name }}</span>
                            </p>
                            {{-- ユーザーのメールアドレス --}}
                            <p class="mb-1 truncate">
                                メールアドレス<span class="font-normal">・・・・</span>
                                <span class="border-b border-slate-400">{{ $warning_user->email }}</span>
                            </p>
                        </div>
                        {{-- ボタンエリア --}}
                        <div class="mt-2 md:w-[30%] flex md:justify-end">
                            {{-- 元に戻すボタン --}}
                            <form class="mr-3" action="{{ route('admin.warning.undo') }}" method="post">
                                @csrf
                                @method('patch')
                                {{-- 選択されているメモのidを取得 --}}
                                <input type="hidden" name="userId" value="{{ $warning_user->id }}">
                                <button class="btn bg-blue-800 hover:bg-blue-700" type="submit">利用再開</button>
                            </form>
                            {{-- 完全削除ボタン --}}
                            <form onsubmit="return deleteCheck()" action="{{ route('admin.warning.destroy') }}"
                                  method="post">
                                @csrf
                                @method('delete')
                                {{-- 選択されているメモのidを取得 --}}
                                <input type="hidden" name="userId" value="{{ $warning_user->id }}">
                                <button class="btn bg-red-600 hover:bg-red-500" type="submit">完全削除</button>
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

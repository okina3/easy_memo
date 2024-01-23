<x-app-layout>
    <x-common.flash-message status="session('status')"/>
    {{-- メールアドレスの検索エリア --}}
    <section class="mb-5 p-3 max-w-screen-lg mx-auto border border-gray-400 rounded-lg bg-gray-200">
        <form action="{{ route('admin.index') }}" method="get">
            <div class="flex space-x-2 items-center">
                <div class="text-gray-600 text-xl font-semibold">
                    メールアドレスから検索・・・
                </div>
                <input name="keyword" class="py-2 w-60 border border-gray-500 rounded-lg"
                       placeholder="メールアドレスを入力">
                <button class="py-1 px-3 rounded text-white bg-blue-800 hover:bg-blue-700">
                    検索する
                </button>
            </div>
        </form>
    </section>
    {{-- ユーザー一覧表示エリア --}}
    <section class="max-w-screen-lg mx-auto text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
        <div class="px-3 py-2 flex justify-between items-center border-b border-gray-400 bg-gray-200">
            <h1 class="text-xl font-semibold">ユーザー一覧</h1>
        </div>
        <div class="p-2 h-[75vh] overflow-y-scroll overscroll-none">
            @foreach ($users_all as $user)
                <div class="mb-5 p-2 flex justify-between items-center border border-gray-400 rounded-lg">
                    <div class="truncate">
                        {{-- ユーザー名 --}}
                        <div class="mb-1">
                            ユーザー名・・・・・・
                            <div class="inline-block font-semibold border-b border-slate-400">
                                {{ $user->name }}
                            </div>
                        </div>
                        {{-- メールアドレス --}}
                        <div class="mb-1">
                            メールアドレス・・・・
                            <div class="inline-block font-semibold border-b border-slate-400">
                                {{ $user->email }}
                            </div>
                        </div>
                    </div>
                    {{-- 削除ボタン --}}
                    <form onsubmit="return deleteCheck()" action="{{ route('admin.destroy') }}" method="post">
                        @csrf
                        @method('delete')
                        {{-- 選択されているユーザーのidを取得 --}}
                        <input type="hidden" name="userId" value="{{ $user->id }}">
                        <button type="submit" class="py-1 px-3 rounded text-white bg-red-600 hover:bg-red-500">
                            利用停止
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    </section>
    <script>
        'use strict'

        // 削除のアラート
        function deleteCheck() {
            const RESULT = confirm('本当に利用停止してもいいですか?');
            if (!RESULT) alert("キャンセルしました");
            return RESULT;
        }
    </script>
</x-app-layout>

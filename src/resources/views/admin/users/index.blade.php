<x-app-layout>
    {{-- フラッシュメッセージ --}}
    <x-common.flash-message status="session('status')"/>
    {{-- ユーザーの検索の表示エリア --}}
    <section class="mb-5 p-3 border border-gray-400 rounded-lg bg-gray-200">
        <form action="{{ route('admin.index') }}" method="get">
            <div class="flex space-x-2 items-center">
                <div class="heading text-gray-600">メールアドレスから検索・・・</div>
                {{-- メールアドレスを入力 --}}
                <input class="py-2 w-60 border border-gray-500 rounded-lg" name="keyword"
                       placeholder="メールアドレスを入力">
                {{-- 検索するボタン --}}
                <button class="btn bg-blue-800 hover:bg-blue-700">検索する</button>
            </div>
        </form>
    </section>
    {{-- 登録ユーザー一覧の表示エリア --}}
    <section class="text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
        {{-- タイトル --}}
        <div class="heading_bg"><h1 class="heading">ユーザー一覧</h1></div>
        {{-- 登録ユーザー一覧 --}}
        <div class="p-2 h-[75vh] overflow-y-scroll overscroll-none">
            @foreach ($users_all as $user)
                <div class="mb-5 p-2 flex justify-between items-center border border-gray-400 rounded-lg">
                    <div class="truncate">
                        {{-- ユーザーの名前 --}}
                        <div class="mb-1">
                            <span class="font-semibold">ユーザー名</span>
                            <span>・・・・・・</span>
                            <span class="font-semibold border-b border-slate-400">
                                {{ $user->name }}
                            </span>
                        </div>
                        {{-- ユーザーのメールアドレス --}}
                        <div class="mb-1">
                            <span class="font-semibold">メールアドレス</span>
                            <span>・・・・</span>
                            <span class="font-semibold border-b border-slate-400">
                                {{ $user->email }}
                            </span>
                        </div>
                    </div>
                    {{-- 利用停止ボタン --}}
                    <form onsubmit="return deleteCheck()" action="{{ route('admin.destroy') }}" method="post">
                        @csrf
                        @method('delete')
                        {{-- 選択されているユーザーのidを取得 --}}
                        <input type="hidden" name="userId" value="{{ $user->id }}">
                        <button class="btn bg-red-600 hover:bg-red-500" type="submit">利用停止</button>
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

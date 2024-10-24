<x-app-layout>
    <div class="max-w-7xl mx-auto px-2 py-2 bg-rose-100 shadow">
        <div class="max-w-screen-lg mx-auto">
            {{-- フラッシュメッセージ --}}
            <x-common.flash-message status="session('status')"/>
            {{-- ユーザーの検索の表示エリア --}}
            <section class="mb-5 px-3 py-2 text-slate-100 border border-gray-500 rounded-lg bg-sky-900">
                <form action="{{ route('admin.index') }}" method="get">
                    <div class="sm:flex items-center">
                        <div class="heading">メールアドレスから検索</div>
                        <div class="hidden sm:block">・・・</div>
                        {{-- メールアドレスを入力 --}}
                        <input class="py-2 w-60 border border-gray-500 rounded-lg" name="keyword"
                               placeholder="メールアドレスを入力">
                        {{-- 検索するボタン --}}
                        <button class="ml-2 btn btn-bk bg-yellow-500 hover:bg-yellow-400">検索する</button>
                    </div>
                </form>
            </section>
            {{-- 登録ユーザー一覧の表示エリア --}}
            <section class="text-gray-600 border border-gray-500 rounded-lg overflow-hidden">
                {{-- タイトル --}}
                <h1 class="heading heading_bg bg-rose-900">ユーザー 一覧</h1>
                {{-- 登録ユーザー一覧 --}}
                <div class="p-2 h-[73vh] overflow-y-scroll overscroll-none bg-white">
                    @foreach ($all_users as $user)
                        <div class="mb-5 p-2 md:flex justify-between items-center border border-gray-500 rounded-lg">
                            <div class="md:w-4/5 font-semibold">
                                {{-- ユーザーの名前 --}}
                                <p class="mb-1 truncate">
                                    ユーザー名<span class="font-normal">・・・・・・</span>
                                    <span class="border-b border-slate-400">{{ $user->name }}</span>
                                </p>
                                {{-- ユーザーのメールアドレス --}}
                                <p class="mb-1 truncate">
                                    メールアドレス<span class="font-normal">・・・・</span>
                                    <span class="border-b border-slate-400">{{ $user->email }}</span>
                                </p>
                            </div>
                            {{-- 利用停止ボタン --}}
                            <div class="md:w-1/5">
                                <form class="md:flex justify-end" onsubmit="return deleteCheck()"
                                      action="{{ route('admin.destroy') }}" method="post">
                                    @csrf
                                    @method('delete')
                                    {{-- 選択されているユーザーのidを取得 --}}
                                    <input type="hidden" name="userId" value="{{ $user->id }}">
                                    <button class="btn bg-red-600 hover:bg-red-500" type="submit">利用停止</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
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

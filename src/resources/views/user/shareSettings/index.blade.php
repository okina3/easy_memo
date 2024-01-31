<x-app-layout>
    {{-- フラッシュメッセージ --}}
    <x-common.flash-message status="session('status')"/>
    <div class="mb-2 flex justify-between">
        {{-- ユーザー検索の表示エリア --}}
        <section class="w-1/5 text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
            {{-- タイトル --}}
            <h1 class="heading heading_bg">ユーザーから検索</h1>
            {{-- ユーザーの検索 --}}
            <div class="p-3 h-[85vh] overflow-y-scroll overscroll-none">
                <div class="mb-2 hover:font-semibold"><a href="share-setting/">全てのメモを表示</a></div>
                {{-- ユーザー一覧 --}}
                @foreach ($shared_users as $shared_user)
                    {{-- 暗号化してurlに値を渡す --}}
                    <a class="mb-1 block truncate hover:font-semibold"
                       href="share-setting/?user={{ encrypt($shared_user->id) }}">
                        {{ $shared_user->name }}
                    </a>
                @endforeach
            </div>
        </section>
        {{-- 共有中のメモ一覧の表示エリア --}}
        <section class="ml-2 w-4/5 text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
            {{-- タイトル --}}
            <h1 class="heading heading_bg">共有されているメモ</h1>
            {{-- 共有中のメモ一覧 --}}
            <div class="p-2 h-[85vh] overflow-y-scroll overscroll-none">
                @foreach ($shared_memos as $shared_memo)
                    <div class="mb-5 p-2 border border-gray-400 rounded-lg">
                        {{-- 共有メモの情報エリア --}}
                        <div class="mb-2 truncate">
                            {{-- 共有中のメモのユーザーの名前 --}}
                            <div class="mb-2 flex items-center font-semibold">
                                <p class="text-blue-700 border-b border-slate-500">{{ $shared_memo->user->name }}</p>
                                <p class="ml-1">さん のメモ</p>
                            </div>
                            {{-- メモのタイトル --}}
                            <p class="sub_heading mb-1">{{ $shared_memo->title }}</p>
                            {{-- メモの内容 --}}
                            <p>{{ $shared_memo->content }}</p>
                        </div>
                        {{-- ボタンエリア --}}
                        <div class="flex justify-end text-white">
                            {{-- メモの詳細ボタン --}}
                            <button class="btn mr-3 bg-gray-800 hover:bg-gray-700"
                                    onclick="location.href='{{ route('user.share-setting.show', ['share' => $shared_memo->id]) }}'">
                                詳細
                            </button>
                            {{-- メモの編集ボタン --}}
                            @if ($shared_memo->access)
                                <button class="btn mr-3 bg-blue-800 hover:bg-blue-700"
                                        onclick="location.href='{{ route('user.share-setting.edit', ['share' => $shared_memo->id]) }}'">
                                    編集
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</x-app-layout>

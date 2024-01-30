<x-app-layout>
    {{-- フラッシュメッセージ --}}
    <x-common.flash-message status="session('status')"/>
    <div class="mb-2 flex justify-between">
        {{-- ユーザー検索の表示エリア --}}
        <section class="w-1/5 text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
            {{-- タイトル --}}
            <div class="heading_bg"><h1 class="heading">ユーザーから検索</h1></div>
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
            <div class="heading_bg"><h1 class="heading">共有されているメモ</h1></div>
            {{-- 共有中のメモ一覧 --}}
            <div class="p-2 h-[85vh] overflow-y-scroll overscroll-none">
                @foreach ($shared_memos as $shared_memo)
                    <div class="mb-5 p-2 border border-gray-400 rounded-lg">
                        {{-- 共有中のメモのユーザーの名前 --}}
                        <div class="mb-2 flex items-center font-semibold">
                            <div class="text-blue-700 border-b border-slate-500">
                                {{ $shared_memo->user->name }}
                            </div>
                            <div class="ml-1">さん のメモ</div>
                        </div>
                        {{-- メモのタイトル --}}
                        <div class="sub_heading mb-1">{{ $shared_memo->title }}</div>
                        {{-- メモの内容 --}}
                        <div class="mb-2 truncate">{{ $shared_memo->content }}</div>
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

<x-app-layout>
    <div class="px-2 py-2 bg-slate-200">
        {{-- フラッシュメッセージ --}}
        <x-common.flash-message status="session('status')"/>
        <div class="mb-2 md:flex justify-between">
            {{-- タグ検索の表示エリア --}}
            <section
                class="mb-2 md:mb-0 md:w-1/5 text-gray-600 border border-gray-500 rounded-lg overflow-hidden shadow">
                {{-- タイトル --}}
                <h1 class="heading heading_bg">タグから検索</h1>
                {{-- タグの検索 --}}
                <div class="p-3 h-[15vh] md:h-[85vh] overflow-y-scroll overscroll-none bg-white">
                    <div class="mb-2 hover:font-semibold"><a href="/">全てのメモを表示</a></div>
                    {{-- タグ一覧 --}}
                    @foreach ($all_tags as $tag)
                        <a class="mb-1 block truncate hover:font-semibold" href="/?tag={{ $tag->id }}">
                            {{ $tag->name }}
                        </a>
                    @endforeach
                </div>
            </section>
            {{-- メモ一覧の表示エリア --}}
            <section class="md:ml-2 md:w-4/5 text-gray-600 border border-gray-500 rounded-lg overflow-hidden">
                {{-- タイトル --}}
                <div class="heading_bg py-1.5 flex justify-between items-center">
                    <h1 class="heading">メモ一覧</h1>
                    {{-- メモ新規作成ボタン --}}
                    <button class="btn btn-bk bg-yellow-500 hover:bg-yellow-400"
                            onclick="location.href='{{ route('user.create') }}'">
                        メモ新規作成
                    </button>
                </div>
                {{-- メモ一覧 --}}
                <div class="p-2 h-[60vh] md:h-[85vh] overflow-y-scroll overscroll-none bg-white">
                    @foreach ($all_memos as $memo)
                        <div class="mb-5 p-2 border border-gray-400 rounded-lg shadow">
                            {{-- 共有中のメモの目印 --}}
                            @if ($memo->status)
                                <div class="mark_bg"><p class="mark">{{ $memo->status }}</p></div>
                            @endif
                            {{-- メモの情報エリア --}}
                            <div class="mb-2">
                                {{-- メモのタイトル --}}
                                <p class="sub_heading mb-1 truncate">{{ $memo->title }}</p>
                                {{-- メモの内容 --}}
                                <p class="truncate">{{ $memo->content }}</p>
                            </div>
                            {{-- ボタンエリア --}}
                            <div class="flex justify-end text-white">
                                {{-- 詳細ボタン --}}
                                <button class="btn mr-3 bg-sky-900 hover:bg-sky-700"
                                        onclick="location.href='{{ route('user.show', ['memo' => $memo->id]) }}'">
                                    詳細
                                </button>
                                {{-- 編集ボタン --}}
                                <button class="btn mr-3 bg-violet-700 hover:bg-violet-500"
                                        onclick="location.href='{{ route('user.edit', ['memo' => $memo->id]) }}'">
                                    編集
                                </button>
                                {{-- 削除ボタン --}}
                                <form onsubmit="return deleteCheck()" action="{{ route('user.destroy') }}"
                                      method="post">
                                    @csrf
                                    @method('delete')
                                    {{-- 選択されているメモのidを取得 --}}
                                    <input type="hidden" name="memoId" value="{{ $memo->id }}">
                                    <button class="btn bg-red-600 hover:bg-red-500" type="submit">削除</button>
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
            const RESULT = confirm('共有設定も解除されますが、本当に削除してもいいですか?');
            if (!RESULT) alert("削除をキャンセルしました");
            return RESULT;
        }
    </script>
</x-app-layout>

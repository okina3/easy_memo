<x-app-layout>
    {{-- フラッシュメッセージ --}}
    <x-common.flash-message status="session('status')"/>
    <div class="mb-2 flex justify-between">
        {{-- タグ検索の表示エリア --}}
        <section class="w-1/5 text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
            {{-- タイトル --}}
            <h1 class="heading heading_bg">タグから検索</h1>
            {{-- タグの検索 --}}
            <div class="p-3 h-[85vh] overflow-y-scroll overscroll-none">
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
        <section class="ml-2 w-4/5 text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
            {{-- タイトル --}}
            <div class="heading_bg py-2 flex justify-between items-center">
                <h1 class="heading">メモ一覧</h1>
                {{-- メモ新規作成ボタン --}}
                <button class="btn bg-blue-800 hover:bg-blue-700" onclick="location.href='{{ route('user.create') }}'">
                    メモ新規作成
                </button>
            </div>
            {{-- メモ一覧 --}}
            <div class="p-2 h-[85vh] overflow-y-scroll overscroll-none">
                @foreach ($all_memos as $memo)
                    <div class="mb-5 p-2 border border-gray-400 rounded-lg">
                        {{-- 共有中のメモの目印 --}}
                        @if ($memo->status)
                            <div class="mark_bg"><p class="mark">{{ $memo->status }}</p></div>
                        @endif
                        {{-- メモの情報エリア --}}
                        <div class="mb-2 truncate">
                            {{-- メモのタイトル --}}
                            <p class="sub_heading mb-1">{{ $memo->title }}</p>
                            {{-- メモの内容 --}}
                            <p>{{ $memo->content }}</p>
                        </div>
                        {{-- ボタンエリア --}}
                        <div class="flex justify-end text-white">
                            {{-- 詳細ボタン --}}
                            <button class="btn mr-3 bg-gray-800 hover:bg-gray-700"
                                    onclick="location.href='{{ route('user.show', ['memo' => $memo->id]) }}'">
                                詳細
                            </button>
                            {{-- 編集ボタン --}}
                            <button class="btn mr-3 bg-blue-800 hover:bg-blue-700"
                                    onclick="location.href='{{ route('user.edit', ['memo' => $memo->id]) }}'">
                                編集
                            </button>
                            {{-- 削除ボタン --}}
                            <form onsubmit="return deleteCheck()" action="{{ route('user.destroy') }}" method="post">
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

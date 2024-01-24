<x-app-layout>
    <x-common.flash-message status="session('status')"/>
    <div class="mb-2 flex justify-between">
        {{-- タグ検索の表示エリア --}}
        <section class="w-1/5 text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
            {{-- タイトル --}}
            <div class="px-3 py-2.5 border-b border-gray-400 bg-gray-200">
                <h1 class="text-xl font-semibold">タグから検索</h1>
            </div>
            {{-- タグの検索 --}}
            <div class="p-3 h-[85vh] overflow-y-scroll overscroll-none">
                <div class="mb-2 hover:font-semibold">
                    <a href="/">全てのメモを表示</a>
                </div>
                {{-- タグ一覧 --}}
                @foreach ($all_tags as $tag)
                    <a href="/?tag={{ $tag->id }}" class="mb-1 block truncate hover:font-semibold">
                        {{ $tag->name }}
                    </a>
                @endforeach
            </div>
        </section>
        {{-- メモ一覧の表示エリア --}}
        <section class="ml-2 w-4/5 text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
            {{-- タイトル --}}
            <div class="px-3 py-2 flex justify-between items-center border-b border-gray-400 bg-gray-200">
                <h1 class="text-xl font-semibold">メモ一覧</h1>
                <button onclick="location.href='{{ route('user.create') }}'"
                        class="py-1 px-3 text-white rounded bg-blue-800 hover:bg-blue-700">
                    メモ新規作成
                </button>
            </div>
            {{-- メモ一覧 --}}
            <div class="p-2 h-[85vh] overflow-y-scroll overscroll-none">
                @foreach ($all_memos as $memo)
                    <div class="mb-5 p-2 border border-gray-400 rounded-lg">
                        {{-- 共有中のメモの目印 --}}
                        @if ($memo->status)
                            <div class="mb-1 inline-block rounded-xl bg-cyan-600">
                                <div class="py-0.5 px-2 text-sm text-white font-semibold ">
                                    {{ $memo->status }}
                                </div>
                            </div>
                        @endif
                        {{-- メモのタイトル --}}
                        <div class="mb-1 text-lg font-semibold">{{ $memo->title }}</div>
                        {{-- メモの内容 --}}
                        <div class="mb-2 truncate">{{ $memo->content }}</div>
                        {{-- ボタンエリア --}}
                        <div class="flex justify-end text-white">
                            {{-- 詳細ボタン --}}
                            <button onclick="location.href='{{ route('user.show', ['memo' => $memo->id]) }}'"
                                    class="mr-3 px-3 py-1 rounded bg-gray-800 hover:bg-gray-700">
                                詳細
                            </button>
                            {{-- 編集ボタン --}}
                            <button onclick="location.href='{{ route('user.edit', ['memo' => $memo->id]) }}'"
                                    class="mr-3 py-1 px-3 rounded bg-blue-800 hover:bg-blue-700">
                                編集
                            </button>
                            {{-- 削除ボタン --}}
                            <form onsubmit="return deleteCheck()" action="{{ route('user.destroy') }}" method="post">
                                @csrf
                                @method('delete')
                                {{-- 選択されているメモのidを取得 --}}
                                <input type="hidden" name="memoId" value="{{ $memo->id }}">
                                <button type="submit" class="py-1 px-3 rounded bg-red-600 hover:bg-red-500">
                                    削除
                                </button>
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

<x-app-layout>
    <section class="max-w-screen-lg mx-auto text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
        {{-- 削除済みメモの管理ページのタイトル --}}
        <div class="heading_bg"><h1 class="heading">削除済みメモ一覧</h1></div>
        {{-- 削除済みメモを管理するエリア --}}
        <div class="p-3 h-[85vh] overflow-y-scroll overscroll-none">
            {{-- フラッシュメッセージ --}}
            <x-common.flash-message status="session('status')"/>
            {{-- ソフトデリートされたメモ一覧 --}}
            @foreach ($trashed_memos as $trashed_memo)
                <div class="py-3 flex justify-between items-center border-b border-slate-300">
                    <div class="mr-10 truncate">
                        {{-- メモのタイトル --}}
                        <div class="sub_heading mb-1">{{ $trashed_memo->title }}</div>
                        {{-- メモの内容 --}}
                        <div class="">{{ $trashed_memo->content }}</div>
                    </div>
                    {{-- ボタンエリア --}}
                    <div class="flex justify-between">
                        {{-- 元に戻すボタン --}}
                        <form class="mr-3" action="{{ route('user.trashed-memo.undo') }}" method="post">
                            @csrf
                            @method('patch')
                            {{-- 選択されているメモのidを取得 --}}
                            <input type="hidden" name="memoId" value="{{ $trashed_memo->id }}">
                            <button class="btn w-24 bg-blue-800 hover:bg-blue-700" type="submit">元に戻す</button>
                        </form>
                        {{-- 完全削除ボタン --}}
                        <form onsubmit="return deleteCheck()" action="{{ route('user.trashed-memo.destroy') }}"
                              method="post">
                            @csrf
                            @method('delete')
                            {{-- 選択されているメモのidを取得 --}}
                            <input type="hidden" name="memoId" value="{{ $trashed_memo->id }}">
                            <button type="submit" class="btn w-24 bg-red-600 hover:bg-red-500">完全削除</button>
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

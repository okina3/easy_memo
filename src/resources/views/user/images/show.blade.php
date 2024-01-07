<x-app-layout>
    <section class="max-w-screen-lg mx-auto text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
        <div class="px-3 py-2 flex justify-between items-center border-b border-gray-400 bg-gray-200">
            <h1 class="py-1 text-xl font-semibold">画像の詳細</h1>
        </div>
        <div class="p-3">
            <x-common.flash-message status="session('status')"/>
            {{-- 選択画像の表示 --}}
            <div class="p-2 w-2/3 mx-auto">
                <div class="relative">
                    <img src="{{ asset('storage/' . $show_image->filename) }}" alt="編集したい画像が表示されます">
                </div>
            </div>
            {{-- 画像の削除ボタン --}}
            <div class="mt-3 mr-2 flex justify-center">
                <form onsubmit="return deleteCheck()" action="{{ route('user.image.destroy') }}" method="post">
                    @csrf
                    @method('delete')
                    {{-- 選択されているメモのidを取得 --}}
                    <input type="hidden" name="memoId" value="{{ $show_image->id }}">
                    <button type="submit" class="py-1 px-3 text-white rounded bg-red-600 hover:bg-red-500">
                        画像を削除
                    </button>
                </form>
            </div>
            <div class="my-2 flex justify-end">
                <button onclick="location.href='{{ route('user.image.index') }}'"
                        class="py-1 px-3 text-white rounded bg-gray-800 hover:bg-gray-700">
                    戻る
                </button>
            </div>
        </div>
    </section>
    <script>
        'use strict';

        // 削除のアラート
        function deleteCheck() {
            const RESULT = confirm('本当に削除してもいいですか?');
            if (!RESULT) alert("削除をキャンセルしました");
            return RESULT;
        }
    </script>
</x-app-layout>

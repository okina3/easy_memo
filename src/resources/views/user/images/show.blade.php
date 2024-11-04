<x-app-layout>
    <div class="px-2 py-2 bg-slate-200">
        <section
            class="text-gray-600 border border-gray-400 rounded-lg bg-white overflow-hidden">
            {{-- 画像の詳細ページのタイトル --}}
            <h1 class="heading heading_bg">画像の詳細</h1>
            {{-- 選択した画像の詳細を表示するエリア --}}
            <div class="p-3 h-[85vh]">
                {{-- 選択した画像を表示 --}}
                <div class="p-2 md:w-4/5 mx-auto">
                    <img class="mx-auto" src="{{ asset('storage/' . $select_image->filename) }}"
                         alt="編集したい画像が表示されます">
                </div>
                {{-- 選択した画像を削除するボタン --}}
                <div class="mt-3 mr-2 flex justify-center">
                    <form onsubmit="return deleteCheck()" action="{{ route('user.image.destroy') }}" method="post">
                        @csrf
                        @method('delete')
                        {{-- 選択されている画像のidを取得 --}}
                        <input type="hidden" name="imageId" value="{{ $select_image->id }}">
                        <button class="btn bg-red-600 hover:bg-red-500" type="submit">画像を削除</button>
                    </form>
                </div>
                {{-- 戻るボタン --}}
                <div class="my-2 flex justify-end">
                    <button class="btn bg-gray-800 hover:bg-gray-700"
                            onclick="location.href='{{ route('user.image.index') }}'">
                        戻る
                    </button>
                </div>
            </div>
        </section>
    </div>
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

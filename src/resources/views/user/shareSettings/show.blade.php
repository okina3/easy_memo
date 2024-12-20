<x-app-layout>
    <div class="px-2 py-2 bg-slate-200">
        <section class="min-h-[45vh] text-gray-600 border border-gray-400 rounded-lg bg-white overflow-hidden">
            {{-- 共有中のメモの詳細ページのタイトル --}}
            <h1 class="heading heading_bg">共有のメモ詳細</h1>
            {{-- 選択した共有メモの詳細を表示するエリア --}}
            <div class="p-3">
                {{-- 選択した共有メモのユーザーの名前を表示 --}}
                <div class="mb-5 flex items-center font-semibold">
                    <p class="text-blue-700 border-b border-slate-500">{{ $select_user->name }}</p>
                    <p class="ml-1">さん のメモ</p>
                </div>
                {{-- 選択した共有メモのタイトルを表示 --}}
                <div class="mb-5">
                    <h2 class="sub_heading mb-1">タイトル</h2>
                    <p class="p-2 border border-gray-500 rounded bg-white">
                        {{ $select_memo->title }}
                    </p>
                </div>
                {{-- 選択した共有メモの内容の表示 --}}
                <div class="mb-5">
                    <h2 class="sub_heading mb-1">内容</h2>
                    <textarea class="w-full rounded" name="content" rows="7"
                              disabled>{{ $select_memo->content }}</textarea>
                </div>
                {{-- 選択した共有メモに紐づいたタグの表示 --}}
                <div class="mb-10">
                    <h2 class="sub_heading mb-1">タグ</h2>
                    @foreach ($get_memo_tags_name as $tag_name)
                        <div class="inline mr-3">
                            <input class="mb-1 rounded" type="checkbox" checked disabled/>
                            {{ $tag_name }}
                        </div>
                    @endforeach
                </div>
                {{-- 選択した共有メモに紐づいた画像の表示 --}}
                <div class="mb-10">
                    <h2 class="sub_heading mb-1">画像</h2>
                    {{-- モーダルウィンドウ --}}
                    <x-common.big-select-image :getMemoImages='$get_memo_images'/>
                </div>
                {{-- 戻るボタン --}}
                <div class="mb-2 flex justify-end">
                    <button class="btn bg-gray-800 hover:bg-gray-700"
                            onclick="location.href='{{ route('user.share-setting.index') }}'">
                        戻る
                    </button>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>

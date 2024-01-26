<x-app-layout>
    <section class="min-h-[45vh] text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
        {{-- 共有中のメモの詳細ページのタイトル --}}
        <div class="heading_bg"><h1 class="heading">共有のメモ詳細</h1></div>
        {{-- 選択した共有メモの詳細を表示するエリア --}}
        <div class="p-3">
            {{-- 選択した共有メモのユーザーの名前を表示 --}}
            <div class="mb-5 flex items-center font-semibold">
                <div class="text-blue-700 border-b border-slate-500">
                    {{ $choice_user->name }}
                </div>
                <div class="ml-1">さん のメモ</div>
            </div>
            {{-- 選択した共有メモのタイトルを表示 --}}
            <div class="mb-5">
                <h2 class="sub_heading mb-1">タイトル</h2>
                <div class="p-2 border border-gray-500 rounded bg-white">
                    {{ $choice_memo->title }}
                </div>
            </div>
            {{-- 選択した共有メモの内容の表示 --}}
            <div class="mb-5">
                <h2 class="sub_heading mb-1">内容</h2>
                <textarea class="w-full rounded" name="content" rows="7" placeholder="ここにメモを入力"
                          disabled>{{ $choice_memo->content }}</textarea>
            </div>
            {{-- 選択した共有メモに紐づいたタグの表示 --}}
            <div class="mb-10">
                <h2 class="sub_heading mb-1">タグ</h2>
                @foreach ($memo_in_tags as $tag)
                    <div class="inline mr-3">
                        <input class="mb-1 rounded" type="checkbox" checked disabled/>
                        {{ $tag }}
                    </div>
                @endforeach
            </div>
            {{-- 選択した共有メモに紐づいた画像の表示 --}}
            <div class="mb-10">
                <h2 class="sub_heading mb-1">画像</h2>
                {{-- モーダルウィンドウ --}}
                <x-common.big-select-image :memoInImages='$memo_in_images'/>
            </div>
            {{-- 戻るボタン --}}
            <div class="mb-2 flex justify-end">
                <button class="btn mr-1 bg-gray-800 hover:bg-gray-700"
                        onclick="location.href='{{ route('user.share-setting.index') }}'">
                    戻る
                </button>
            </div>
        </div>
    </section>
</x-app-layout>

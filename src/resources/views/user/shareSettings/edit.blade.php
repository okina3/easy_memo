<x-app-layout>
    <section class="min-h-[45vh] text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
        {{-- 共有中のメモの編集ページのタイトル --}}
        <h1 class="heading heading_bg">共有のメモ編集</h1>
        {{-- 選択した共有メモを編集するエリア --}}
        <div class="p-3">
            {{-- 選択した共有メモのユーザーの名前を表示 --}}
            <div class="mb-5 flex items-center font-semibold">
                <p class="text-blue-700 border-b border-slate-500">{{ $select_user->name }}</p>
                <p class="ml-1">さん のメモ</p>
            </div>
            {{-- コメント --}}
            <p class="mb-5">※「内容」のみ編集可能。</p>
            {{-- 編集中の共有メモの表示 --}}
            <form action="{{ route('user.share-setting.update') }}" method="post">
                @csrf
                @method('patch')
                {{-- 選択した共有メモのタイトルを表示 --}}
                <div class="mb-5">
                    <h2 class="sub_heading mb-1">タイトル</h2>
                    <p class="p-2 border border-gray-500 rounded bg-white">{{ $select_memo->title }}</p>
                </div>
                {{-- 選択した共有メモの内容を表示 --}}
                <div class="mb-5">
                    <h2 class="sub_heading mb-1">内容</h2>
                    <textarea class="w-full rounded" name="content" rows="7"
                              placeholder="ここにメモを入力">{{ $select_memo->content }}</textarea>
                    {{-- エラーメッセージ （メモの内容）--}}
                    <x-input-error class="mt-2" :messages="$errors->get('content')"/>
                </div>
                {{-- 選択した共有メモに紐づいたタグの表示 --}}
                <div class="mb-10">
                    <h2 class="sub_heading mb-1">タグ</h2>
                    @foreach ($get_memo_tags as $tag)
                        <div class="inline mr-3">
                            <input class="mb-1 rounded" type="checkbox" checked disabled/>
                            {{ $tag }}
                        </div>
                    @endforeach
                </div>
                {{-- 選択した共有メモに紐づいた画像を表示 --}}
                <div class="mb-10">
                    <h2 class="sub_heading mb-1">画像</h2>
                    {{-- モーダルウィンドウ --}}
                    <x-common.big-select-image :getMemoImages='$get_memo_images'/>
                </div>
                {{-- 選択されている共有メモのidを取得 --}}
                <input type="hidden" name="memoId" value="{{ $select_memo->id }}">
                {{-- 更新するボタン --}}
                <div class="mb-5">
                    <button class="btn bg-blue-800 hover:bg-blue-700" type="submit">更新する</button>
                </div>
            </form>
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

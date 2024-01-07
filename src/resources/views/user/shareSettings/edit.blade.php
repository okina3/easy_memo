<x-app-layout>
    <section class="min-h-[45vh] text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
        <div class="px-3 py-2 border-b border-gray-400 bg-gray-200">
            <h1 class="py-1 text-xl font-semibold">共有のメモ編集</h1>
        </div>
        <div class="p-3">
            {{-- 共有メモのユーザーの名前 --}}
            <div class="mb-5 flex items-center font-semibold">
                <div class="text-blue-700 border-b border-slate-500">
                    {{ $choice_user->name }}
                </div>
                <div class="ml-1">さん のメモ</div>
            </div>
            <div class="mb-5">
                ※「内容」のみ編集可能。
            </div>
            <form action="{{ route('user.share-setting.update') }}" method="post">
                @csrf
                @method('patch')
                {{-- 選択されているメモのidを取得 --}}
                <input type="hidden" name="memoId" value="{{ $choice_memo->id }}">
                {{-- タイトルの表示エリア --}}
                <div class="mb-5">
                    <h1 class="mb-1 text-lg font-semibold">タイトル</h1>
                    <div class="p-2 border border-gray-500 rounded bg-white">
                        {{ $choice_memo->title }}
                    </div>
                </div>
                {{-- 選択したメモの内容表示エリア --}}
                <div class="mb-5">
                    <h1 class="mb-1 text-lg font-semibold">内容</h1>
                    <textarea class="w-full rounded" name="content" rows="7"
                              placeholder="ここにメモを入力">{{ $choice_memo->content }}</textarea>
                    {{-- メモの内容エラーメッセージ --}}
                    <x-input-error :messages="$errors->get('content')" class="mt-2"/>
                </div>
                {{-- 選択タグの表示エリア --}}
                <div class="mb-10">
                    <h1 class="mb-1 text-lg font-semibold">タグ</h1>
                    @foreach ($memo_in_tags as $tag)
                        <div class="inline mr-3">
                            <input type="checkbox" class="mb-1 rounded" checked disabled/>
                            {{ $tag }}
                        </div>
                    @endforeach
                </div>

                {{-- 選択画像の表示 --}}
                <div class="mb-10">
                    <h1 class="mb-1 text-lg font-semibold">画像</h1>
                    {{-- モーダルウィンドウ --}}
                    <x-common.big-select-image :memoInImages='$memo_in_images'/>
                </div>
                <div class="mb-5">
                    <button type="submit" class="py-1 px-4 text-white rounded bg-blue-800 hover:bg-blue-700">
                        更新する
                    </button>
                </div>
            </form>
            <div class="mb-2 flex justify-end">
                <button onclick="location.href='{{ route('user.share-setting.index') }}'"
                        class="mr-1 py-1 px-3 text-white rounded bg-gray-800 hover:bg-gray-700">
                    戻る
                </button>
            </div>
        </div>
    </section>
</x-app-layout>

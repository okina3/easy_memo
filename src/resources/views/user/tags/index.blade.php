<x-app-layout>
    <div class="max-w-7xl mx-auto px-2 py-2 bg-slate-300 shadow">
        <section
            class="max-w-screen-lg mx-auto text-gray-600 border border-gray-400 rounded-lg bg-white overflow-hidden">
            {{-- タグの管理ページのタイトル --}}
            <h1 class="heading heading_bg">タグ一覧</h1>
            {{-- タグを管理するエリア --}}
            <div class="p-3">
                {{-- フラッシュメッセージ --}}
                <x-common.flash-message status="session('status')"/>
                {{-- タグを新規作成するエリア --}}
                <form action="{{ route('user.tag.store') }}" method="post">
                    @csrf
                    <div class="mb-10">
                        {{-- タイトル --}}
                        <h2 class="sub_heading mb-1">新規タグ作成</h2>
                        {{-- 新規タグの入力 --}}
                        <input class="mb-2 block form-control rounded w-60" type="text" name="new_tag"
                               placeholder="ここに新規タグを入力"/>
                        {{-- タグを保存するボタン --}}
                        <button class="btn bg-blue-800 hover:bg-blue-700" type="submit">保存</button>
                        {{-- エラーメッセージ （新規タグ）--}}
                        <x-input-error class="mt-2" :messages="$errors->get('new_tag')"/>
                    </div>
                </form>
                {{-- タグを削除するエリア --}}
                <form onsubmit="return deleteCheck()" action="{{ route('user.tag.destroy') }}" method="post">
                    @csrf
                    @method('delete')
                    <div class="mb-5">
                        {{-- タイトル --}}
                        <h2 class="sub_heading mb-1">既存タグの削除</h2>
                        <p class="mb-1 text-sm">
                            ※タグ一覧から、削除したいタグをチェックして「タグを削除」を押してください。
                        </p>
                        <h2 class="sub_heading mb-1">タグ一覧</h2>
                        {{-- タグ一覧 --}}
                        @foreach ($all_tags as $tag)
                            <div class="inline mr-3 border-b border-slate-500 hover:font-semibold">
                                <input class="mb-1 rounded" type="checkbox" name="tags[]" id="{{ $tag->id }}"
                                       value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }} />
                                <label for="{{ $tag->id }}">{{ $tag->name }}</label>
                            </div>
                        @endforeach
                        {{-- エラーメッセージ （タグの削除） --}}
                        <x-input-error class="mt-5" :messages="$errors->get('tags')"/>
                    </div>
                    {{-- タグを削除するボタン --}}
                    <button class="btn bg-red-600 hover:bg-red-500" type="submit">タグを削除</button>
                </form>
                {{-- 戻るボタン --}}
                <div class="my-2 flex justify-end">
                    <button class="btn bg-gray-800 hover:bg-gray-700"
                            onclick="location.href='{{ route('user.index') }}'">
                        戻る
                    </button>
                </div>
            </div>
        </section>
    </div>
    <script>
        'use strict'

        //削除のアラート
        function deleteCheck() {
            const RESULT = confirm('本当に削除してもいいですか?');
            if (!RESULT) alert("削除をキャンセルしました");
            return RESULT;
        }
    </script>
</x-app-layout>

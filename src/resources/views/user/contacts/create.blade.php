<x-app-layout>
    <section class="max-w-screen-lg mx-auto text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
        {{-- 問い合わせページのタイトル --}}
        <h1 class="heading heading_bg">管理人にお問い合わせ</h1>
        <div class="p-3">
            {{-- フラッシュメッセージ --}}
            <x-common.flash-message status="session('status')"/>
            {{-- 問い合わせするエリア --}}
            <form action="{{ route('user.contact.store') }}" method="post">
                @csrf
                <div class="mb-10">
                    {{-- 件名の入力 --}}
                    <div class="mb-3">
                        <h2 class="sub_heading mb-1">件名</h2>
                        <input class="block form-control rounded w-60" type="text" name="subject"
                               value="{{ old('subject') }}"
                               placeholder="ここに件名を入力"/>
                        {{-- エラーメッセージ （件名）--}}
                        <x-input-error class="mt-2" :messages="$errors->get('subject')"/>
                    </div>
                    {{-- お問い合わせ内容の入力 --}}
                    <div class="mb-3">
                        <h2 class="sub_heading mb-1">お問い合わせ内容</h2>
                        <textarea class="w-full rounded" name="message" rows="7"
                                  placeholder="ここに問い合わせ内容を入力">{{ old('message') }}</textarea>
                        {{-- エラーメッセージ （問い合わせ内容）--}}
                        <x-input-error class="mt-2" :messages="$errors->get('message')"/>
                    </div>
                    {{-- 問い合わせ内容を送信するボタン --}}
                    <button class="mt-2 btn bg-blue-800 hover:bg-blue-700" type="submit">送信</button>
                </div>
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
</x-app-layout>

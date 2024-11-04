<x-app-layout>
    <div class="px-2 py-2 bg-rose-100">
        <section class="text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
            {{-- 問い合わせ情報の詳細ページのタイトル --}}
            <h1 class="heading heading_bg bg-rose-900">ユーザーからの問い合わせの詳細</h1>
            {{-- 選択した問い合わせ情報の詳細を表示するエリア --}}
            <div class="p-3 h-[85vh] bg-white">
                <div class="mb-3">
                    {{-- 選択した問い合わせ情報のユーザー名を表示 --}}
                    <div class="mb-5">
                        <h2 class="sub_heading mb-1">ユーザー名</h2>
                        <p class="p-2 border border-gray-500 rounded">{{ $select_contact->user->name }}</p>
                    </div>
                    {{-- 選択した問い合わせ情報のメールアドレスを表示 --}}
                    <div class="mb-5">
                        <h2 class="sub_heading mb-1">メールアドレス</h2>
                        <p class="p-2 border border-gray-500 rounded">{{ $select_contact->user->email }}</p>
                    </div>
                    {{-- 選択した問い合わせ情報の件名を表示 --}}
                    <div class="mb-5">
                        <h2 class="sub_heading mb-1">件名</h2>
                        <p class="p-2 border border-gray-500 rounded">{{ $select_contact->subject }}</p>
                    </div>
                    {{-- 選択した問い合わせ情報の内容を表示 --}}
                    <div class="mb-5">
                        <h2 class="sub_heading mb-1">問い合わせ内容</h2>
                        <textarea class="w-full rounded" name="content" rows="7"
                                  disabled>{{ $select_contact->message }}</textarea>
                    </div>
                    {{-- ボタンエリア --}}
                    {{-- 削除ボタン --}}
                    <form action="{{ route('admin.contact.destroy') }}" method="post">
                        @csrf
                        @method('delete')
                        {{-- 選択されている問い合わせ情報のidを取得 --}}
                        <input type="hidden" name="contentId" value="{{ $select_contact->id }}">
                        <button class="btn bg-red-600 hover:bg-red-500" type="submit">削除</button>
                    </form>
                    {{-- 戻るボタン --}}
                    <div class="mb-2 flex justify-end">
                        <button onclick="location.href='{{ route('admin.contact.index') }}'"
                                class="btn bg-gray-800 hover:bg-gray-700">
                            戻る
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>

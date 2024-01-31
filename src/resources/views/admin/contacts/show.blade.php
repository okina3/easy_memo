<x-app-layout>
    <section class="min-h-[45vh] text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
        {{-- 問い合わせ情報の詳細ページのタイトル --}}
        <h1 class="heading heading_bg">メモ詳細</h1>
        {{-- 選択した問い合わせ情報の詳細を表示するエリア --}}
        <div class="p-3">
            <div class="mb-3">
                {{-- 選択した問い合わせ情報のユーザー名を表示 --}}
                <div class="mb-5">
                    <h2 class="sub_heading mb-1">ユーザー名</h2>
                    <p class="p-2 border border-gray-500 rounded bg-white">{{'ユーザー名'}}</p>
                </div>
                {{-- 選択した問い合わせ情報のメールアドレスを表示 --}}
                <div class="mb-5">
                    <h2 class="sub_heading mb-1">メールアドレス</h2>
                    <p class="p-2 border border-gray-500 rounded bg-white">{{'メールアドレス'}}</p>
                </div>
                {{-- 選択した問い合わせ情報の件名を表示 --}}
                <div class="mb-5">
                    <h2 class="sub_heading mb-1">件名</h2>
                    <p class="p-2 border border-gray-500 rounded bg-white">{{'タイトル'}}</p>
                </div>
                {{-- 選択した問い合わせ情報の内容を表示 --}}
                <div class="mb-5">
                    <h2 class="sub_heading mb-1">問い合わせ内容</h2>
                    <textarea class="w-full rounded" name="content" rows="7" disabled>{{'内容'}}</textarea>
                </div>
                {{-- 戻るボタン --}}
                <div class="mb-2 flex justify-end">
                    <button onclick="location.href='{{ route('admin.contact.index') }}'"
                            class="btn mr-1 bg-gray-800 hover:bg-gray-700">
                        戻る
                    </button>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>

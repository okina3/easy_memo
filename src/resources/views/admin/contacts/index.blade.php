<x-app-layout>
    {{-- フラッシュメッセージ --}}
    <x-common.flash-message status="session('status')"/>
    <div class="max-w-screen-lg mx-auto">
        {{-- 検索の表示エリア --}}
        <section class="mb-5 px-3 py-2 text-gray-600 border border-gray-400 rounded-lg bg-gray-200">
            <form action="{{ route('admin.contact.index') }}" method="get">
                <div class="flex space-x-2 items-center">
                    <div class="heading">キーワードから検索・・・・・</div>
                    {{-- キーワードを入力 --}}
                    <input class="py-2 w-60 border border-gray-500 rounded-lg" name="keyword"
                           placeholder="キーワードを入力">
                    {{-- 検索するボタン --}}
                    <button class="btn bg-blue-800 hover:bg-blue-700">検索する</button>
                </div>
            </form>
            {{-- コメント --}}
            <p class="text-sm mt-2">※ キーワードは、件名、問い合わせ内容の、両方から検索します。</p>
        </section>
        {{-- ユーザーからの問い合わせ一覧の表示エリア --}}
        <section class="text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
            {{-- タイトル --}}
            <h1 class="heading heading_bg">ユーザーからの問い合わせ一覧</h1>
            {{-- ユーザーからの問い合わせ一覧 --}}
            <div class="p-2 h-[72vh] overflow-y-scroll overscroll-none">
                @foreach ($all_contact as $contact)
                    <div class="mb-5 p-2 flex justify-between items-center border border-gray-400 rounded-lg">
                        <div class="w-[88%] mr-5 font-semibold">
                            {{-- ユーザー名 --}}
                            <p class="mb-1 truncate">
                                ユーザー名<span class="font-normal">・・・・・</span>
                                <span class="border-b border-slate-400">{{ $contact->user->name }}</span>
                            </p>
                            {{-- 件名 --}}
                            <p class="mb-1 truncate">
                                件名<span class="font-normal">・・・・・・・・</span>{{ $contact->subject }}
                            </p>
                            {{-- 問い合わせ内容 --}}
                            <p class="mb-1 truncate">
                                問い合わせ内容<span class="font-normal">・・・</span>{{ $contact->message }}
                            </p>
                        </div>
                        {{-- 詳細ボタン --}}
                        <div class="w-[12%] flex justify-end">
                            <button class="btn bg-gray-800 hover:bg-gray-700"
                                    onclick="location.href='{{ route('admin.contact.show', ['contact' => $contact->id]) }}'">
                                詳細
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</x-app-layout>

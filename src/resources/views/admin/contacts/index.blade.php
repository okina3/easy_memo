<x-app-layout>
    <div class="px-2 py-2 bg-rose-100">
        {{-- フラッシュメッセージ --}}
        <x-common.flash-message status="session('status')"/>
        {{-- 検索の表示エリア --}}
        <section class="mb-5 px-3 py-2 text-slate-100 border border-gray-500 rounded-lg bg-rose-900">
            <form action="{{ route('admin.contact.index') }}" method="get">
                <div class="sm:flex items-center">
                    <div class="heading">キーワードから検索</div>
                    <div class="hidden sm:block">・・・・・</div>
                    {{-- キーワードを入力 --}}
                    <input class="py-2 w-60 border border-gray-500 rounded-lg" name="keyword"
                           placeholder="キーワードを入力">
                    {{-- 検索するボタン --}}
                    <button class="ml-2 btn btn-bk bg-yellow-500 hover:bg-yellow-400">検索する</button>
                </div>
            </form>
            {{-- コメント --}}
            <p class="text-sm mt-2">※ キーワードは、件名、問い合わせ内容の、両方から検索します。</p>
        </section>
        {{-- ユーザーからの問い合わせ一覧の表示エリア --}}
        <section class="text-gray-600 border border-gray-500 rounded-lg overflow-hidden bg-white">
            {{-- タイトル --}}
            <h1 class="heading heading_bg bg-rose-900">ユーザーからの問い合わせ一覧</h1>
            {{-- ユーザーからの問い合わせ一覧 --}}
            <div class="p-2 h-[73vh] overflow-y-scroll overscroll-none">
                @foreach ($all_contact as $contact)
                    <div class="mb-5 p-2 md:flex justify-between items-center border border-gray-500 rounded-lg">
                        <div class="md:w-[88%] mr-5 font-semibold">
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
                        <div class="md:w-[12%] flex justify-end">
                            <button class="btn bg-sky-900 hover:bg-sky-700"
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

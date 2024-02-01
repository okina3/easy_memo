<x-app-layout>
    {{-- フラッシュメッセージ --}}
    <x-common.flash-message status="session('status')"/>
    <div class="max-w-screen-lg mx-auto">
        {{-- ユーザーからの問い合わせ一覧の表示エリア --}}
        <section class="text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
            {{-- タイトル --}}
            <h1 class="heading heading_bg">ユーザーからの連絡一覧</h1>
            {{-- ユーザーからの問い合わせ一覧 --}}
            <div class="p-2 h-[75vh] overflow-y-scroll overscroll-none">
                @foreach ($all_contact as $contact)
                    <div class="mb-5 p-2 flex justify-between items-center border border-gray-400 rounded-lg">
                        <div class="mr-5 font-semibold truncate">
                            {{-- ユーザー名 --}}
                            <p class="mb-1">
                                ユーザー名<span class="font-normal">・・・・・</span>
                                <span class="border-b border-slate-400">{{ $contact->user->name }}</span>
                            </p>
                            {{-- 件名 --}}
                            <p class="mb-1">
                                件名<span class="font-normal">・・・・・・・・</span>{{ $contact->subject }}
                            </p>
                            {{-- 問い合わせ内容 --}}
                            <p class="mb-1">
                                問い合わせ内容<span class="font-normal">・・・</span>{{ $contact->message }}
                            </p>
                        </div>
                        {{-- 詳細ボタン --}}
                        <button class="btn mr-3 w-16 bg-gray-800 hover:bg-gray-700"
                                onclick="location.href='{{ route('admin.contact.show', ['contact' => $contact->id]) }}'">
                            詳細
                        </button>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</x-app-layout>

<x-app-layout>
    {{-- フラッシュメッセージ --}}
    <x-common.flash-message status="session('status')"/>
    <div class="max-w-screen-lg mx-auto">
        {{-- ユーザーからの問い合わせ一覧の表示エリア --}}
        <section class="text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
            {{-- タイトル --}}
            <div class="heading_bg"><h1 class="heading">ユーザーからの問い合わせ一覧</h1></div>
            {{-- ユーザーからの問い合わせ一覧 --}}
            <div class="p-2 h-[75vh] overflow-y-scroll overscroll-none">
                @foreach ($contact_all as $contact)
                <div class="mb-5 p-2 flex justify-between items-center border border-gray-400 rounded-lg">
                    <div class="font-semibold truncate">
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
                    {{-- 利用停止ボタン --}}
                    <form onsubmit="return deleteCheck()" action="{{ route('admin.destroy') }}" method="post">
                        @csrf
                        @method('delete')
                        {{-- 選択されている問い合わせ情報のidを取得 --}}
                        <input type="hidden" name="contactId" value="{{ $contact->id }}">
                        <button class="btn bg-red-600 hover:bg-red-500" type="submit">詳細</button>
                    </form>
                </div>
                @endforeach
            </div>
        </section>
    </div>
</x-app-layout>

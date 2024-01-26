<x-app-layout>
    <section class="max-w-screen-lg mx-auto text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
        {{-- 画像一覧表示ページのタイトル --}}
        <div class="heading_bg py-2 flex justify-between items-center">
            <h1 class="heading">登録画像一覧</h1>
            {{-- 画像新規登録ボタン --}}
            <button class="btn mr-1 bg-blue-800 hover:bg-blue-700"
                    onclick="location.href='{{ route('user.image.create') }}'">
                画像新規登録
            </button>
        </div>
        {{-- 登録した画像の表示エリア --}}
        <div class="p-3">
            {{-- フラッシュメッセージ --}}
            <x-common.flash-message status="session('status')"/>
            <div class="flex flex-wrap">
                {{-- 登録画像の一覧  --}}
                @foreach ($images as $image)
                    <div class="w-1/4 p-1 mb-5">
                        <div class="p-1 border border-gray-300 rounded-md">
                            <a href="{{ route('user.image.show', ['image' => $image->id]) }}">
                                <img src="{{ asset('storage/' . $image->filename) }}" alt="登録した画像が表示されます">
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="m-2">
                {{ $images->links() }}
            </div>
        </div>
    </section>
</x-app-layout>

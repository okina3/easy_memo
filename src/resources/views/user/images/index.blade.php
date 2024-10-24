<x-app-layout>
    <div class="max-w-7xl mx-auto px-2 py-2 bg-slate-300 shadow">
        <section
            class="max-w-screen-lg mx-auto text-gray-600 border border-gray-400 rounded-lg bg-white overflow-hidden">
            {{-- 画像一覧表示ページのタイトル --}}
            <div class="heading_bg py-1.5 flex justify-between items-center">
                <h1 class="heading">登録画像一覧</h1>
                {{-- 画像新規登録ボタン --}}
                <button class="btn btn-bk bg-yellow-500 hover:bg-yellow-400"
                        onclick="location.href='{{ route('user.image.create') }}'">
                    画像新規登録
                </button>
            </div>
            {{-- 登録した画像の表示エリア --}}
            <div class="mb:p-3">
                {{-- フラッシュメッセージ --}}
                <x-common.flash-message status="session('status')"/>
                <div class="flex flex-wrap">
                    {{-- 登録画像の一覧  --}}
                    @foreach ($all_images as $image)
                        <div class=" p-1 w-1/2 sm:w-1/3 md:w-1/4">
                            <div class="p-1 border border-gray-300 rounded-md">
                                <a href="{{ route('user.image.show', ['image' => $image->id]) }}">
                                    <img src="{{ asset('storage/' . $image->filename) }}"
                                         alt="登録した画像が表示されます">
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="m-2">
                    {{ $all_images->links() }}
                </div>
            </div>
        </section>
    </div>
</x-app-layout>

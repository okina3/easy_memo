<x-app-layout>
    <section class="max-w-screen-lg mx-auto text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
        <div class="px-3 py-2 flex justify-between items-center border-b border-gray-400 bg-gray-200">
            <h1 class="py-1 text-xl font-semibold">登録画像一覧</h1>
            <div class="mr-1 flex justify-end">
                <button onclick="location.href='{{ route('user.image.create') }}'"
                        class="py-1 px-4 text-white rounded bg-blue-800 hover:bg-blue-700">
                    画像新規登録
                </button>
            </div>
        </div>
        <div class="p-3">
            <x-common.flash-message status="session('status')"/>
            {{-- 登録画像の一覧表示  --}}
            <div class="flex flex-wrap">
                @foreach ($images as $image)
                    <div class="w-1/4 p-1 mb-5">
                        <div class="p-1 border border-gray-300 rounded-md">
                            <a href="{{ route('user.image.show', ['image' => $image->id]) }}">
                                {{-- 画像 --}}
                                <img src="{{ asset('storage/' . $image->filename) }}" alt="登録した画像が表示されます">
                                {{-- 画像のタイトル --}}
                                <div class="text-gray-700 text-center">{{ $image->title }}</div>
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

<x-app-layout>
    <section class="max-w-screen-lg mx-auto text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
        {{-- 画像の登録ページのタイトル --}}
        <h1 class="heading heading_bg">画像の登録</h1>
        {{-- 画像を新規登録するエリア --}}
        <div class="p-3">
            {{-- エラーメッセージ（画像）--}}
            <x-input-error :messages="$errors->get('images')" class="mt-2"/>
            <form action="{{ route('user.image.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                {{-- 登録する画像の選択 --}}
                <div class="m-2">
                    <div class="p-2 sm:w-2/3 mx-auto">
                        <div class="mt-2">
                            <h2 class="sub_heading mb-1">画像</h2>
                            <input class="py-1 px-3 w-full border border-gray-300 rounded bg-gray-100" type="file"
                                   id="image" name="images" accept="image/png,image/jpeg,image/jpg">
                        </div>
                    </div>
                </div>
                {{-- 画像を登録するボタン --}}
                <div class="mt-4 p-2 w-full flex justify-center">
                    <button class="btn bg-blue-800 hover:bg-blue-700" type="submit">登録</button>
                </div>
            </form>
            {{-- 戻るボタン --}}
            <div class="mb-2 flex justify-end">
                <button class="btn mr-1 bg-gray-800 hover:bg-gray-700"
                        onclick="location.href='{{ route('user.image.index') }}'">
                    戻る
                </button>
            </div>
        </div>
    </section>
</x-app-layout>

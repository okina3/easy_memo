<x-app-layout>
   <section class="min-h-[45vh] text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
      <div class="px-3 py-2 border-b border-gray-400 bg-gray-200">
         <h1 class="text-xl font-semibold">共有のメモ詳細</h1>
      </div>
      <div class="p-3">
         {{-- 共有メモのユーザーの名前 --}}
         <div class="mb-5 flex items-center font-semibold">
            <div class="text-blue-700 border-b border-slate-500">
               {{ $choice_user->name }}
            </div>
            <div class="ml-1">さん のメモ</div>
         </div>
         {{-- タイトルの表示エリア --}}
         <div class="mb-5">
            <h1 class="mb-1 text-lg font-semibold">タイトル</h1>
            <div class="p-2 border border-gray-500 rounded bg-white">
               {{ $choice_memo->title }}
            </div>
         </div>
         {{-- 内容の表示エリア --}}
         <div class="mb-5">
            <h1 class="mb-1 text-lg font-semibold">内容</h1>
            <textarea class="w-full rounded" name="content" rows="7" placeholder="ここにメモを入力" disabled>{{ $choice_memo->content }}</textarea>
         </div>
         {{-- 選択タグの表示エリア --}}
         <div class="mb-10">
            <h1 class="mb-1 text-lg font-semibold">タグ</h1>
            @foreach ($memo_in_tags as $tag)
               <div class="inline mr-3">
                  <input type="checkbox" class="mb-1 rounded" checked disabled />
                  {{ $tag }}
               </div>
            @endforeach
         </div>

         {{-- 選択画像の表示 --}}
         <div class="mb-10">
            <h1 class="mb-1 text-lg font-semibold">画像</h1>
            {{-- モーダルウィンドウ --}}
            <x-common.big-select-image :memoInImages='$memo_in_images' />
         </div>
         <div class="mb-2 flex justify-end">
            <button onclick="location.href='{{ route('user.share-setting.index') }}'"
               class="mr-1 py-1 px-3 text-white rounded bg-gray-800 hover:bg-gray-700">
               戻る
            </button>
         </div>
      </div>
   </section>
</x-app-layout>

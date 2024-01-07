<x-app-layout>
   <section class="max-w-screen-lg mx-auto text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
      <div class="px-3 py-2 flex justify-between items-center border-b border-gray-400 bg-gray-200">
         <h1 class="py-1 text-xl font-semibold">削除済みメモ一覧</h1>
      </div>
      <div class="p-3 ">
         <x-common.flash-message status="session('status')" />
         @foreach ($trashed_memos as $trashed_memo)
            <div class="py-3 flex justify-between items-center border-b border-slate-300">
               {{-- ソフトデリートされたメモ一覧 --}}
               <div class="mr-10 truncate">
                  <div class="mb-1 text-lg font-semibold">{{ $trashed_memo->title }}</div>
                  {{-- タイトル --}}
                  {{ $trashed_memo->content }}
               </div>
               <div class="flex justify-between">
                  {{-- 元に戻すボタン --}}
                  <form action="{{ route('user.trashed-memo.undo') }}" method="post" class="mr-3">
                     @csrf
                     @method('patch')
                     {{-- 選択されているメモのidを取得 --}}
                     <input type="hidden" name="memoId" value="{{ $trashed_memo->id }}">
                     <button type="submit" class="py-1 px-2 w-24 text-white rounded bg-blue-800 hover:bg-blue-700">
                        元に戻す
                     </button>
                  </form>
                  {{-- 完全削除ボタン --}}
                  <form onsubmit="return deleteCheck()" action="{{ route('user.trashed-memo.destroy') }}"
                     method="post">
                     @csrf
                     @method('delete')
                     {{-- 選択されているメモのidを取得 --}}
                     <input type="hidden" name="memoId" value="{{ $trashed_memo->id }}">
                     <button type="submit" class="py-1 px-2 w-24 text-white rounded bg-red-600 hover:bg-red-500">
                        完全削除
                     </button>
                  </form>
               </div>
            </div>
         @endforeach
      </div>
   </section>
   <script>
      'use strict'

      // 削除のアラート
      function deleteCheck() {
         const RESULT = confirm('本当に削除してもいいですか?');
         if (!RESULT) alert("削除をキャンセルしました");
         return RESULT;
      }
   </script>
</x-app-layout>

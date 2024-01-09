<x-app-layout>
   <x-common.flash-message status="session('status')" />
   <div class="mb-2 flex justify-between">
      {{-- メールアドレスの検索エリア --}}
      <section class="w-1/5 text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
         <div class="px-3 py-2.5 border-b border-gray-400 bg-gray-200">
            <h1 class="text-xl font-semibold">メールアドレスから検索</h1>
         </div>
         <div class="p-3 h-[90vh] overflow-y-scroll overscroll-none">
                {{-- <div class="mb-2 hover:font-semibold">
                    <a href="/">全てのメモを表示</a>
                </div>
                @foreach ($all_tags as $tag)
                    <a href="/?tag={{ $tag->id }}" class="mb-1 block truncate hover:font-semibold">
                        {{ $tag->name }}
                    </a>
                @endforeach --}}
            </div>
      </section>
      {{-- ユーザー一覧表示エリア --}}
      <section class="ml-2 w-4/5 text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
         <div class="px-3 py-2.5 flex justify-between items-center border-b border-gray-400 bg-gray-200">
            <h1 class="text-xl font-semibold">ユーザー一覧</h1>
         </div>
         <div class="p-2 h-[90vh] overflow-y-scroll overscroll-none">
            @foreach ($users_all as $user)
               <div class="mb-5 p-2 flex justify-between items-center border border-gray-400 rounded-lg">
                  <div class="">
                     {{-- ユーザー名 --}}
                     <div class="mb-1">
                           ユーザー名・・・・・・
                        <div class="inline-block font-semibold border-b border-slate-400">
                           {{ $user->name }}
                        </div>
                     </div>
                     {{-- メールアドレス --}}
                     <div class="mb-1">
                           メールアドレス・・・・
                        <div class="inline-block font-semibold border-b border-slate-400">
                           {{ $user->email }}
                        </div>
                     </div>
                  </div>
                  {{-- 削除ボタン --}}
                  <form onsubmit="return deleteCheck()" action="{{ route('admin.destroy') }}" method="post">
                     @csrf
                     @method('delete')
                     {{-- 選択されているユーザーのidを取得 --}}
                     <input type="hidden" name="userId" value="{{ $user->id }}">
                     <button type="submit" class="py-1 px-3 rounded text-white bg-red-600 hover:bg-red-500">
                        削除
                     </button>
                  </form>
               </div>
            @endforeach
         </div>
      </section>
   </div>
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

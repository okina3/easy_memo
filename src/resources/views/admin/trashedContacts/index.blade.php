<x-app-layout>
   {{-- フラッシュメッセージ --}}
   <x-common.flash-message status="session('status')"/>
   <div class="max-w-screen-lg mx-auto">
       {{-- ユーザーからの問い合わせ一覧の表示エリア --}}
       <section class="text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
           {{-- タイトル --}}
           <h1 class="heading heading_bg">削除済みユーザーからの連絡一覧</h1>
           {{-- ユーザーからの問い合わせ一覧 --}}
           <div class="p-2 h-[75vh] overflow-y-scroll overscroll-none">
               @foreach ($trashed_contacts as $contact)
                   <div class="mb-5 p-2 flex justify-between items-center border border-gray-400 rounded-lg">
                       <div class="font-semibold truncate">
                           {{-- 件名 --}}
                           <p class="mb-1">
                               件名<span class="font-normal">・・・・・・・・</span>{{ $contact->subject }}
                           </p>
                           {{-- 問い合わせ内容 --}}
                           <p class="mb-1">
                               問い合わせ内容<span class="font-normal">・・・</span>{{ $contact->message }}
                           </p>
                       </div>
                        {{-- ボタンエリア --}}
                        <div class="flex justify-between">
                           {{-- 元に戻すボタン --}}
                           <form action="{{ route('admin.trashed-contact.undo') }}" method="post" class="mr-3">
                               @csrf
                               @method('patch')
                               {{-- 選択されている問い合わせのidを取得 --}}
                               <input type="hidden" name="contentId" value="{{ $contact->id }}">
                               <button class="btn w-24 bg-blue-800 hover:bg-blue-700" type="submit">元に戻す</button>
                           </form>
                           {{-- 完全削除ボタン --}}
                           <form onsubmit="return deleteCheck()" action="{{ route('admin.trashed-contact.destroy') }}"
                                 method="post">
                               @csrf
                               @method('delete')
                               {{-- 選択されている問い合わせのidを取得 --}}
                               <input type="hidden" name="contentId" value="{{ $contact->id }}">
                               <button class="btn w-24 bg-red-600 hover:bg-red-500" type="submit">完全削除</button>
                           </form>
                       </div>
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
<x-app-layout>
   <section class="max-w-screen-lg mx-auto text-gray-600 border border-gray-400 rounded-lg overflow-hidden">
       {{-- 問い合わせページのタイトル --}}
       <div class="heading_bg"><h1 class="heading">問い合わせ</h1></div>
       <div class="p-3">
           {{-- フラッシュメッセージ --}}
           <x-common.flash-message status="session('status')"/>
           {{-- 問い合わせするエリア --}}
           <form action="{{ route('user.tag.store') }}" method="post">
               @csrf
               <div class="mb-10">
                   {{-- タイトル --}}
                   <h2 class="sub_heading mb-1">新規タグ作成</h2>
                   {{-- 新規タグの入力 --}}
                   <div class="mr-5 mb-2">
                       <input class="form-control rounded w-60" type="text" name="new_tag"
                              placeholder="ここに新規タグを入力"/>
                   </div>
                   {{-- タグを保存するボタン --}}
                   <button class="btn bg-blue-800 hover:bg-blue-700" type="submit">保存</button>
                   {{-- エラーメッセージ （新規タグ）--}}
                   <x-input-error class="mt-2" :messages="$errors->get('new_tag')"/>
               </div>
           </form>
           {{-- 戻るボタン --}}
           <div class="my-2 flex justify-end">
               <button class="btn bg-gray-800 hover:bg-gray-700"
                       onclick="location.href='{{ route('user.index') }}'">
                   戻る
               </button>
           </div>
       </div>
   </section>
</x-app-layout>
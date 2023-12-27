 {{-- 属性による値の受け取り --}}
 @php
    $memoInImagesId = $memoInImagesId ?? '';
 @endphp

 {{-- モーダルウィンドウ --}}
 <div class="modal micromodal-slide" id="modal-1" aria-hidden="true">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close>
       <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
          {{-- モーダルウィンドウでのタイトル --}}
          <header class="modal__header">
             <h2 class="modal__title" id="modal-1-title">
                画像をクリックしてください（４枚まで可）
             </h2>
             <button type="button" class="modal__close" aria-label="Close modal" data-micromodal-close></button>
          </header>
          {{-- モーダルウィンドウでの画像一覧 --}}
          <main class="modal__content" id="modal-1-content">
             <div class="flex flex-wrap">
                @foreach ($allImages as $image)
                   <div class="w-1/4 p-1">
                      <div class="border rounded-md relative">
                         {{-- チェックボタンを、操作できなくするレイヤー --}}
                         <div class="w-full h-8 z-10 absolute bg-black opacity-0"></div>
                         <label class="mx-2 mb-2 block cursor-pointer">
                            <input class="my-2 imageCheckbox" type="checkbox" name="images[]" value="{{ $image->id }}"
                               {{ $memoInImagesId && in_array($image->id, $memoInImagesId) ? 'checked' : '' }} />
                            <img class="image" data-id="{{ $image->id }}" data-file="{{ $image->filename }}"
                               data-path="{{ asset('storage/') }}" src="{{ asset('storage/' . $image->filename) }}">
                         </label>
                      </div>
                   </div>
                @endforeach
             </div>
          </main>
       </div>
    </div>
 </div>
 {{-- ブラウザの表示 --}}
 <div class="mt-1">
    {{-- ブラウザの画像サムネイル --}}
    <div id="thumbnail-area" class="flex flex-wrap"></div>
    {{-- ブラウザの画像選択ボタン --}}
    <div class="mt-2">
       <a class="p-2 hover:font-semibold border border-gray-300 rounded-md" data-micromodal-trigger="modal-1"
          href='javascript:'>画像を選択してください
       </a>
    </div>
 </div>

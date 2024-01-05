{{-- モーダルウィンドウ --}}
<div class="flex flex-wrap">
   @foreach ($memoInImages as $memo_image)
      {{-- 拡大画像、モーダルウィンドウ --}}
      <div class="modal micromodal-slide" id="{{ $memo_image->id }}" aria-hidden="true">
         <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="{{ $memo_image->id }}-title">
               <main class="modal__content" id="{{ $memo_image->id }}-content">
                  <img src="{{ asset('storage/' . $memo_image->filename) }}">
               </main>
            </div>
         </div>
      </div>
      {{-- ブラウザの表示 --}}
      {{-- ブラウザのサムネイル画像 --}}
      <div class="w-1/5 p-1">
         <a data-micromodal-trigger="{{ $memo_image->id }}" href='javascript:'>
            <img src="{{ asset('storage/' . $memo_image->filename) }}">
         </a>
      </div>
   @endforeach
</div>

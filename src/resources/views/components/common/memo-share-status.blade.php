{{-- メモの共有設定を表示するエリア（アコーディオン） --}}
<div id="shared-information">
    {{-- アコーディオンの開閉ボタン --}}
    <div id="shared-button">
        <button type="button" class="accordion-button">メモの共有設定</button>
    </div>
    {{-- エラーメッセージ（共有設定） --}}
    <div class="mt-2">
        <x-input-error :messages="$errors->get('share_user_start')"/>
        <x-input-error :messages="$errors->get('share_user_end')"/>
    </div>
    {{-- メモの共有設定エリア --}}
    <div class="accordion-body">
        <div class="border-b-4 border-gray-700">
            {{-- メモの共有を開始するエリア --}}
            <form action="{{ route('user.share-setting.store') }}" method="post">
                @csrf
                {{-- 選択されているメモのidを取得 --}}
                <input type="hidden" name="memoId" value="{{ $choiceMemoId }}">
                <div class="mb-3 pb-5 border-b border-gray-400">
                    {{-- 共有設定のタイトル --}}
                    <h1 class="mb-1 text-lg font-semibold">このメモを共有する</h1>
                    {{-- ユーザーのメールアドレスを入力 --}}
                    <div class="mb-3">
                        <p class="text-sm">共有したいユーザーのメールアドレスを入力してください。</p>
                        <input type="text" class="w-60 rounded" name="share_user_start"
                               placeholder="メールアドレスを入力">
                    </div>
                    {{-- 編集権限のボタン --}}
                    <div class="mb-3">
                        <p class="text-sm">共有したユーザーに、メモの編集を許可しますか？</p>
                        <input type="radio" class="ml-2" name="edit_access" id="yes_access" value=1
                            {{ old('edit_access') == 1 ? 'checked' : '' }} />
                        <label for="yes_access">はい</label>
                        <input type="radio" class="ml-10" name="edit_access" id="no_access" value=0
                            {{ old('edit_access') == 0 ? 'checked' : '' }} />
                        <label for="no_access">いいえ</label>
                    </div>
                    {{-- メモを共有するボタン --}}
                    <button type="submit"
                            class="py-1 px-3 block text-white rounded bg-cyan-600 hover:bg-cyan-700">
                        共有する
                    </button>
                </div>
            </form>
            {{-- メモの共有状態を詳しく表示するエリア --}}
            <div class="mb-3 pb-4 border-b border-gray-400">
                {{-- 共有設定のタイトル --}}
                <h1 class="mb-1 text-lg font-semibold">共有中のユーザー</h1>
                {{-- 共有中のユーザーの情報を表示 --}}
                <div
                    class="p-2 max-h-[25vh] border border-gray-400 rounded bg-white overflow-y-scroll overscroll-none">
                    @foreach ($sharedUsers as $shared_user)
                        <p class="mb-1 border-b border-gray-400">
                        {{-- 共有中のユーザーの名前 --}}
                        <p>ユーザー名・・・・<span class="font-semibold">{{ $shared_user->name }}</span></p>
                        {{-- 共有中のユーザーのメールアドレス --}}
                        <p>メールアドレス・・<span class="font-semibold">{{ $shared_user->email }}</span></p>
                        {{-- 共有中のメモのアクセス許可の判定 --}}
                        <p>アクセス許可・・・
                            @if ($shared_user->access === 1)
                                <span class="font-semibold">
                                                       詳細、<span class=" text-blue-800">編集</span>も可
                                                   </span>
                            @endif
                            @if ($shared_user->access === 0)
                                <span class="font-semibold">詳細のみ</span>
                            @endif
                        </p>
                    @endforeach
                </div>
            </div>
            {{-- メモの共有を停止するエリア --}}
            <form action="{{ route('user.share-setting.destroy') }}" method="post">
                @csrf
                @method('delete')
                {{-- 選択されているメモのidを取得 --}}
                <input type="hidden" name="memoId" value="{{ $choiceMemoId }}">
                <div class="mb-3 pb-5">
                    {{-- 共有設定のタイトル --}}
                    <h1 class="mb-1 text-lg font-semibold">このメモの共有を停止する</h1>
                    {{-- ユーザーのメールアドレスを入力 --}}
                    <p class="text-sm">共有停止したいユーザーのメールアドレスを入力してください。</p>
                    <input type="text" class="mb-2 w-60 rounded" name="share_user_end"
                           placeholder="メールアドレスを入力">
                    {{-- メモの共有を停止するボタン --}}
                    <button type="submit"
                            class="py-1 px-3 block text-white rounded bg-cyan-600 hover:bg-cyan-700">
                        共有停止
                    </button>
                    {{-- コメント --}}
                    <p class="text-sm mt-2">
                        ※ このメモの全てのユーザーの共有を停止したい場合は、メモを削除してください。
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

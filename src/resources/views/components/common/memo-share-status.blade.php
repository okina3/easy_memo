{{-- メモの共有設定を表示するエリア（アコーディオン） --}}
<div id="shared-information">
    {{-- アコーディオンの開閉ボタン --}}
    <div id="shared-button">
        <button class="accordion_button" type="button">メモの共有設定</button>
    </div>
    {{-- エラーメッセージ（共有設定） --}}
    <div class="mt-2">
        <x-input-error :messages="$errors->get('share_user_start')"/>
        <x-input-error :messages="$errors->get('share_user_end')"/>
    </div>
    {{-- メモの共有設定エリア --}}
    <div class="accordion_body">
        <div class="border-b-4 border-gray-700">
            {{-- メモの共有を開始するエリア --}}
            <form class="mb-3 pb-5 border-b border-gray-400" action="{{ route('user.share-setting.store') }}"
                  method="post">
                @csrf
                <h2 class="sub_heading mb-1">このメモを共有する</h2>
                {{-- ユーザーのメールアドレスを入力 --}}
                <div class="mb-3">
                    <p class="text-sm">共有したいユーザーのメールアドレスを入力してください。</p>
                    <input class="w-60 rounded" type="text" name="share_user_start"
                           placeholder="メールアドレスを入力">
                </div>
                {{-- 編集権限のボタン --}}
                <div class="mb-3">
                    <p class="text-sm">共有したユーザーに、メモの編集を許可しますか？</p>
                    <input class="ml-2" type="radio" name="edit_access" id="yes_access" value=1
                        {{ old('edit_access') == 1 ? 'checked' : '' }} />
                    <label for="yes_access">はい</label>
                    <input class="ml-10" type="radio" name="edit_access" id="no_access" value=0
                        {{ old('edit_access') == 0 ? 'checked' : '' }} />
                    <label for="no_access">いいえ</label>
                </div>
                {{-- 選択されているメモのidを取得 --}}
                <input type="hidden" name="memoId" value="{{ $selectMemoId }}">
                {{-- メモを共有するボタン --}}
                <button class="btn bg-blue-800 hover:bg-blue-700" type="submit">共有する</button>
            </form>
            {{-- メモの共有状態を詳しく表示するエリア --}}
            <div class="mb-3 pb-4 border-b border-gray-400">
                <h2 class="sub_heading mb-1">共有中のユーザー</h2>
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
            <form class="mb-3" action="{{ route('user.share-setting.destroy') }}" method="post">
                @csrf
                @method('delete')
                <h2 class="sub_heading mb-1">このメモの共有を停止する</h2>
                {{-- ユーザーのメールアドレスを入力 --}}
                <p class="text-sm">共有停止したいユーザーのメールアドレスを入力してください。</p>
                <input class="mb-2 w-60 rounded" type="text" name="share_user_end"
                       placeholder="メールアドレスを入力">
                {{-- 選択されているメモのidを取得 --}}
                <input type="hidden" name="memoId" value="{{ $selectMemoId }}">
                {{-- メモの共有を停止するボタン --}}
                <button class="btn block bg-red-600 hover:bg-red-500" type="submit">共有停止</button>
            </form>
            {{-- コメント --}}
            <p class="mb-5 text-sm">※ このメモの全てのユーザーの共有を停止したい場合は、メモを削除してください。</p>
        </div>
    </div>
</div>

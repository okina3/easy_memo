<?php

namespace App\Services;

class SessionService
{

    /**
     * ブラウザバック用のセッションに値を設定するメソッド。
     * @return void
     */
    public static function setBrowserBackSession(): void
    {
        session()->flash('back_button_clicked', encrypt(env('BROWSER_BACK_KEY')));
    }

    /**
     * ブラウザバック用のセッションに値がない場合、また、値が一致しない場合のリダイレクトするメソッド。
     * @return void
     */
    public static function clickBrowserBackSession(): void
    {
        if (!session()->has('back_button_clicked') ||
            decrypt(session('back_button_clicked')) !== env('BROWSER_BACK_KEY')) {
            abort(to_route('user.index')
                ->with(['message' => '予期せぬエラーが起きました。トップページに戻ります。', 'status' => 'alert']));
        }
    }

    /**
     * ブラウザバック用のセッションの値を削除するメソッド。
     * @return void
     */
    public static function resetBrowserBackSession(): void
    {
        session()->forget('back_button_clicked');
    }
}

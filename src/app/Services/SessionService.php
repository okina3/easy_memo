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
        session()->flash('back_button_clicked', '値');
    }

    /**
     * ブラウザバック用のセッションに値がない場合のリダイレクトするメソッド。
     * @return void
     */
    public static function clickBrowserBackSession(): void
    {
        if (!session()->has('back_button_clicked')) {
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

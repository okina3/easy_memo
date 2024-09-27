<?php

namespace Tests\User\Unit\Services;

use App\Services\SessionService;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Session;
use Tests\User\TestCase;

class SessionServiceTest extends TestCase
{
    /**
     * ブラウザバック用のセッションに値を設定するテスト。
     */
    public function testSetBrowserBackSession()
    {
        // セッションにブラウザバック用の値を設定するサービスメソッドを実行
        SessionService::setBrowserBackSession();

        // セッションが設定されたことを確認
        $this->assertTrue(Session::has('back_button_clicked'));

        // セッション値を復号し、期待される環境変数の値と比較
        $decryptedSessionValue = decrypt(Session::get('back_button_clicked'));
        $this->assertEquals(env('BROWSER_BACK_KEY'), $decryptedSessionValue);
    }

    /**
     * ブラウザバック用のセッションに値を設定する例外のテスト（BROWSER_BACK_KEYが設定されていない場合）
     */
    public function testErrorSetBrowserBackSession()
    {
        // テスト環境でBROWSER_BACK_KEYをnullに設定
        config(['app.test_browser_back_key' => null]);

        // 例外が投げられることを期待
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('BROWSER_BACK_KEY is not set in the environment file.');

        // メソッドを実行して例外を確認
        SessionService::setBrowserBackSession();
    }

    /**
     * ブラウザバック時にセッションの値を検証し、不正な場合にリダイレクトするテスト。
     */
    public function testClickBrowserBackSession()
    {
        // セッションに正しい値を設定 (環境変数 'BROWSER_BACK_KEY' を暗号化)
        Session::flash('back_button_clicked', encrypt(env('BROWSER_BACK_KEY')));
        // セッション値を検証するサービスメソッドを実行
        SessionService::clickBrowserBackSession();

        // 例外が投げられないことを確認
        $this->assertTrue(true);

        // セッションに不正な値を設定 (文字列 'wrong_key' を暗号化)
        Session::flash('back_button_clicked', encrypt('wrong_key'));
        // 期待される例外のクラスを指定
        $this->expectException(HttpResponseException::class);
        // セッション値を検証するサービスメソッドを実行し、不正な値のため例外が投げられることを確認
        SessionService::clickBrowserBackSession();
    }

    /**
     * ブラウザバック用のセッションの値を削除するテスト。
     */
    public function testResetBrowserBackSession()
    {
        // セッションに値を設定
        Session::flash('back_button_clicked', encrypt(env('BROWSER_BACK_KEY')));
        // セッションに値があることを確認
        $this->assertTrue(Session::has('back_button_clicked'));
        // セッションの値を削除するサービスメソッドを実行
        SessionService::resetBrowserBackSession();

        // セッションの値が削除されたことを確認
        $this->assertFalse(Session::has('back_button_clicked'));
    }
}
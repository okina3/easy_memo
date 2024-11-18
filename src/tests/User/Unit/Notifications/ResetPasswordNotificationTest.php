<?php

namespace Tests\User\Unit\Notifications;

use App\Notifications\User\ResetPasswordNotification;
use Illuminate\Notifications\Messages\MailMessage;
use ReflectionClass;
use stdClass;
use Tests\User\TestCase;

class ResetPasswordNotificationTest extends TestCase
{
    /**
     * メールメッセージの内容をテスト。
     *
     * @return void
     */
    public function testToMail()
    {
        // パスワードリセット通知用のトークンを設定
        $token = 'sample_token';
        // リセットパスワード通知インスタンスを作成
        $notification = new ResetPasswordNotification($token);

        // メールメッセージを生成。通知対象のオブジェクトを作成
        $notifiable = new stdClass();
        // メールメッセージを生成するメソッドを呼び出し
        $mailMessage = $notification->toMail($notifiable);

        // 期待されるパスワードリセットURLを作成
        $url = url("reset-password/$token");
        // メールメッセージが MailMessage クラスのインスタンスであることを確認
        $this->assertInstanceOf(MailMessage::class, $mailMessage);

        // メールメッセージの内容を反射クラスを使用して検証
        $reflectionClass = new ReflectionClass($mailMessage);
        // メールの件名を取得するためのプロパティを取得
        $subject = $reflectionClass->getProperty('subject');
        // メールの挨拶文を取得するためのプロパティを取得
        $greeting = $reflectionClass->getProperty('greeting');
        // メールのアクションテキスト（ボタンのテキスト）を取得するためのプロパティを取得
        $actionText = $reflectionClass->getProperty('actionText');
        // メールのアクションURL（ボタンのリンク先）を取得するためのプロパティを取得
        $actionUrl = $reflectionClass->getProperty('actionUrl');

        // 件名が期待通りの値であることを確認
        $this->assertEquals('ユーザー様 パスワードリセットURLの送付', $subject->getValue($mailMessage));
        // 挨拶文が期待通りの値であることを確認
        $this->assertEquals('いつもご利用頂きありがとうございます', $greeting->getValue($mailMessage));
        // アクションテキストが期待通りの値であることを確認
        $this->assertEquals('パスワードリセット', $actionText->getValue($mailMessage));
        // アクションURLが期待通りの値であることを確認
        $this->assertEquals($url, $actionUrl->getValue($mailMessage));
    }
}

<?php

namespace App\Notifications\User;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    // トークンプロパティを追加
    public string $token;

    /**
     * Create a new notification instance.
     *
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }


    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * 通知のメール表現をする為のメソッド。
     * @param object $notifiable
     * @return MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {
        // URLを生成
        $url = url("reset-password/{$this->token}");

        return (new MailMessage)
            // ->subject(config('app.name') . ' パスワードリセットURLの送付')
            ->subject('ユーザー様' . ' パスワードリセットURLの送付')
            ->greeting('いつもご利用頂きありがとうございます')
            ->action('パスワードリセット', $url)
            ->line('こちらからパスワードリセットを行ってください');
    }
}
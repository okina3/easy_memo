<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

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
        // URLをここで生成
        $url = url("admin/reset-password/{$this->token}");

        return (new MailMessage)
            // ->subject(config('app.name') . ' パスワードリセットURLの送付')
            ->subject('管理者様' . ' パスワードリセットURLの送付')
            ->greeting('いつもご利用頂きありがとうございます')
            ->action('パスワードリセット', $url)
            ->line('こちらからパスワードリセットを行ってください');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}

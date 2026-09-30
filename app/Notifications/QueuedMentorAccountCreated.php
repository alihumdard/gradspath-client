<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class QueuedMentorAccountCreated extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $plainPassword,
        public readonly ?string $loginUrl = null,
        protected ?User $user = null,
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $userName = trim((string) ($notifiable->name ?? '')) ?: 'Mentor';
        $userEmail = (string) ($notifiable->email ?? '');
        $url = $this->loginUrl ?: route('login');

        return (new MailMessage)
            ->subject('Your Grads Paths Mentor Account Credentials')
            ->view('emails.mentor-account-created', [
                'userName' => $userName,
                'email' => $userEmail,
                'password' => $this->plainPassword,
                'loginUrl' => $url,
            ]);
    }

    /**
     * @return array<int, string>
     */
    public function tags(): array
    {
        $targetUser = $this->user ?? ($this->notifiable instanceof User ? $this->notifiable : null);

        if (! $targetUser) {
            return ['notification:mentor-account-created'];
        }

        return array_filter([
            'notification:mentor-account-created',
            'user:'.$targetUser->getKey(),
            $targetUser->email ? 'email:'.$targetUser->email : null,
        ]);
    }
}

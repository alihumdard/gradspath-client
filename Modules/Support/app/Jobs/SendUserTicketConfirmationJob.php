<?php

namespace Modules\Support\app\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Modules\Support\app\Mail\UserSupportTicketConfirmationMail;
use Modules\Support\app\Models\SupportTicket;

class SendUserTicketConfirmationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $ticketId) {}

    public function handle(): void
    {
        $ticket = SupportTicket::query()
            ->with('user')
            ->find($this->ticketId);

        if (!$ticket?->user?->email) {
            return;
        }

        Mail::to($ticket->user->email)->send(new UserSupportTicketConfirmationMail($ticket));
    }
}

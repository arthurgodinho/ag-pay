<?php

namespace App\Listeners;

use App\Events\PaymentFailed;
use App\Services\EmailService;
use App\Models\User;

class SendPaymentFailedEmail
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function handle(PaymentFailed $event)
    {
        $transaction = $event->transaction;
        $user = $transaction->user ?? User::find($transaction->user_id);
        
        if ($user) {
            $this->emailService->sendPaymentFailedEmail($user, $transaction);
        }
    }
}


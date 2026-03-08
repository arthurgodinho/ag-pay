<?php

namespace App\Listeners;

use App\Events\PaymentSent;
use App\Services\EmailService;
use App\Models\User;

class SendPaymentSentEmail
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function handle(PaymentSent $event)
    {
        $transaction = $event->transaction;
        $user = $transaction->user ?? User::find($transaction->user_id);
        
        if ($user) {
            $this->emailService->sendPaymentSentEmail($user, $transaction);
        }
    }
}


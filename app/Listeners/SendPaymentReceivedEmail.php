<?php

namespace App\Listeners;

use App\Events\PaymentReceived;
use App\Services\EmailService;
use App\Models\User;

class SendPaymentReceivedEmail
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function handle(PaymentReceived $event)
    {
        $transaction = $event->transaction;
        $user = $transaction->user ?? User::find($transaction->user_id);
        
        if ($user) {
            $this->emailService->sendPaymentReceivedEmail($user, $transaction);
        }
    }
}


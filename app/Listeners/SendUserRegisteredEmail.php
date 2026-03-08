<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Services\EmailService;

class SendUserRegisteredEmail
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function handle(UserRegistered $event)
    {
        // Envia email de boas-vindas
        $this->emailService->sendUserRegisteredEmail($event->user);
        
        // Se o usuário está com status pending, envia email de análise
        if ($event->user->status === 'pending' || $event->user->status === 'under_review') {
            $this->emailService->sendRegistrationPendingEmail($event->user);
        }
    }
}


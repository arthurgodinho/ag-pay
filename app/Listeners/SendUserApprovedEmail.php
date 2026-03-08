<?php

namespace App\Listeners;

use App\Events\UserApproved;
use App\Services\EmailService;

class SendUserApprovedEmail
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function handle(UserApproved $event)
    {
        $this->emailService->sendAccountApprovedEmail($event->user);
    }
}


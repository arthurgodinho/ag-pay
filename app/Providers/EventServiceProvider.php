<?php

namespace App\Providers;

use App\Events\UserRegistered;
use App\Events\UserApproved;
use App\Events\PaymentReceived;
use App\Events\PaymentSent;
use App\Events\PaymentFailed;
use App\Listeners\SendUserRegisteredEmail;
use App\Listeners\SendUserApprovedEmail;
use App\Listeners\SendPaymentReceivedEmail;
use App\Listeners\SendPaymentSentEmail;
use App\Listeners\SendPaymentFailedEmail;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        UserRegistered::class => [
            SendUserRegisteredEmail::class,
        ],
        UserApproved::class => [
            SendUserApprovedEmail::class,
        ],
        PaymentReceived::class => [
            SendPaymentReceivedEmail::class,
        ],
        PaymentSent::class => [
            SendPaymentSentEmail::class,
        ],
        PaymentFailed::class => [
            SendPaymentFailedEmail::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}


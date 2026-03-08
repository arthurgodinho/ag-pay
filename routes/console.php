<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Agenda o comando de liberação de transações de cartão para rodar a cada hora
Schedule::command('transactions:release-card')->hourly();

// Agenda verificação de transações pendentes via API dos gateways
// Roda a cada 5 minutos para garantir que pagamentos sejam identificados rapidamente
Schedule::command('transactions:check-pending')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground();

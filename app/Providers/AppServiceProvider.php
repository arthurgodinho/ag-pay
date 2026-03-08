<?php

namespace App\Providers;

use App\Models\Transaction;
use App\Observers\TransactionObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registra o Observer para Transaction
        Transaction::observe(TransactionObserver::class);

        // Otimização: Previne N+1 queries e atribuição em massa não permitida em ambiente local
        // Isso ajuda a encontrar bugs durante o desenvolvimento
        \Illuminate\Database\Eloquent\Model::preventLazyLoading(!app()->isProduction());
        \Illuminate\Database\Eloquent\Model::preventSilentlyDiscardingAttributes(!app()->isProduction());

        // Força HTTPS em produção para evitar Mixed Content (Cloudflare/Proxy)
        if (app()->isProduction()) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}

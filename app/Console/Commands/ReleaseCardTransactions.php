<?php

namespace App\Console\Commands;

use App\Services\TransactionReleaseService;
use Illuminate\Console\Command;

class ReleaseCardTransactions extends Command
{
    protected $signature = 'transactions:release-card';
    protected $description = 'Libera transações de cartão de crédito que atingiram o prazo de liberação';

    public function handle(TransactionReleaseService $service): int
    {
        $this->info('Verificando transações de cartão para liberação...');
        
        $released = $service->releaseExpiredCardTransactions();
        
        if ($released > 0) {
            $this->info("{$released} transação(ões) de cartão liberada(s) com sucesso!");
        } else {
            $this->info('Nenhuma transação de cartão para liberar no momento.');
        }

        return Command::SUCCESS;
    }
}

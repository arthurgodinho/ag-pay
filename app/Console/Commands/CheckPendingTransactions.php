<?php

namespace App\Console\Commands;

use App\Services\TransactionStatusService;
use Illuminate\Console\Command;

class CheckPendingTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transactions:check-pending 
                            {--limit= : Limite de transações para verificar}
                            {--gateway= : Verificar apenas um gateway específico}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica status de transações pendentes consultando os gateways via API';

    /**
     * Execute the console command.
     */
    public function handle(TransactionStatusService $service): int
    {
        $this->info('🔍 Verificando status de transações pendentes via API dos gateways...');
        
        $startTime = microtime(true);
        
        try {
            $stats = $service->checkPendingTransactions();
            
            $elapsedTime = round(microtime(true) - $startTime, 2);
            
            $this->info("✅ Verificação concluída em {$elapsedTime}s");
            $this->newLine();
            
            $this->table(
                ['Métrica', 'Valor'],
                [
                    ['Transações Verificadas', $stats['checked']],
                    ['Transações Atualizadas', $stats['updated']],
                    ['Erros', $stats['errors']],
                ]
            );
            
            if (!empty($stats['by_gateway'])) {
                $this->newLine();
                $this->info('📊 Atualizações por Gateway:');
                
                $gatewayRows = [];
                foreach ($stats['by_gateway'] as $gateway => $count) {
                    $gatewayRows[] = [ucfirst($gateway), $count];
                }
                
                $this->table(['Gateway', 'Atualizações'], $gatewayRows);
            }
            
            if ($stats['updated'] > 0) {
                $this->newLine();
                $this->info("🎉 {$stats['updated']} transação(ões) foram atualizadas e o saldo foi creditado!");
            }
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('❌ Erro ao verificar transações pendentes: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            
            return Command::FAILURE;
        }
    }
}


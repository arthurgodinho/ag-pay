<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// ============================================
// TRATAMENTO DE ERROS ANTES DO LARAVEL
// ============================================
// Captura erros de inicialização e mostra mensagens úteis

// Configura exibição de erros (apenas em desenvolvimento)
// Em produção, o Laravel gerencia isso via APP_DEBUG
if (getenv('APP_DEBUG') === 'true' || getenv('APP_ENV') === 'local') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
} else {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
}

// Função para exibir erro de inicialização
function showInitializationError($title, $message, $details = null, $solutions = []) {
    http_response_code(500);
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Erro de Configuração - NexusPay</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
                background: linear-gradient(135deg, #0f1419 0%, #151a23 100%);
                color: #fff;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            .container {
                max-width: 800px;
                width: 100%;
                background: #1a1f2e;
                border-radius: 20px;
                padding: 40px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.5);
                border: 1px solid rgba(255,255,255,0.1);
            }
            .error-icon {
                width: 80px;
                height: 80px;
                background: rgba(239, 68, 68, 0.2);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 30px;
                font-size: 40px;
            }
            h1 {
                color: #ef4444;
                font-size: 28px;
                margin-bottom: 10px;
                text-align: center;
            }
            .message {
                background: rgba(59, 130, 246, 0.1);
                border-left: 4px solid #3b82f6;
                padding: 20px;
                margin: 20px 0;
                border-radius: 8px;
                font-size: 16px;
                line-height: 1.6;
            }
            .details {
                background: rgba(0, 0, 0, 0.3);
                padding: 20px;
                border-radius: 8px;
                margin: 20px 0;
                font-family: 'Courier New', monospace;
                font-size: 14px;
                overflow-x: auto;
                border: 1px solid rgba(255,255,255,0.1);
            }
            .solutions {
                margin-top: 30px;
            }
            .solutions h2 {
                color: #10b981;
                font-size: 20px;
                margin-bottom: 15px;
            }
            .solutions ol {
                margin-left: 20px;
                line-height: 2;
            }
            .solutions li {
                margin-bottom: 10px;
                padding-left: 10px;
            }
            .code {
                background: rgba(0, 0, 0, 0.5);
                padding: 2px 6px;
                border-radius: 4px;
                font-family: 'Courier New', monospace;
                color: #fbbf24;
            }
            .warning {
                background: rgba(251, 191, 36, 0.1);
                border-left: 4px solid #fbbf24;
                padding: 15px;
                margin: 20px 0;
                border-radius: 8px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="error-icon">⚠️</div>
            <h1><?php echo htmlspecialchars($title); ?></h1>
            <div class="message"><?php echo nl2br(htmlspecialchars($message)); ?></div>
            
            <?php if ($details): ?>
            <div class="details">
                <strong>Detalhes do erro:</strong><br>
                <?php echo nl2br(htmlspecialchars($details)); ?>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($solutions)): ?>
            <div class="solutions">
                <h2>🔧 Como corrigir:</h2>
                <ol>
                    <?php foreach ($solutions as $solution): ?>
                    <li><?php echo nl2br(htmlspecialchars($solution)); ?></li>
                    <?php endforeach; ?>
                </ol>
            </div>
            <?php endif; ?>
            
            <div class="warning">
                <strong>💡 Dica:</strong> Após corrigir, limpe o cache do Laravel: <span class="code">php artisan config:clear && php artisan cache:clear</span>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit(1);
}

// Verificações de .env removidas - o Laravel gerencia isso automaticamente
// Isso reduz o tempo de inicialização

// Tenta carregar o autoloader do Composer
if (!file_exists($autoloadPath = __DIR__.'/../vendor/autoload.php')) {
    showInitializationError(
        'Dependências não instaladas',
        'As dependências do projeto não foram instaladas. O arquivo vendor/autoload.php não foi encontrado.',
        'Caminho esperado: ' . $autoloadPath,
        [
            'Execute via SSH: <span class="code">composer install --no-dev --optimize-autoloader</span>',
            'Certifique-se de que o Composer está instalado no servidor',
            'Verifique se você está no diretório correto do projeto'
        ]
    );
}

// Tenta carregar o Composer autoloader
try {
    require $autoloadPath;
} catch (Exception $e) {
    showInitializationError(
        'Erro ao carregar autoloader',
        'Não foi possível carregar o autoloader do Composer.',
        $e->getMessage(),
        [
            'Execute: <span class="code">composer dump-autoload</span>',
            'Verifique se todas as dependências estão instaladas corretamente',
            'Verifique as permissões dos arquivos'
        ]
    );
}

// Verifica se o bootstrap/app.php existe
if (!file_exists($bootstrapPath = __DIR__.'/../bootstrap/app.php')) {
    showInitializationError(
        'Arquivo bootstrap/app.php não encontrado',
        'O arquivo de inicialização do Laravel não foi encontrado.',
        'Caminho esperado: ' . $bootstrapPath,
        [
            'Verifique se todos os arquivos do projeto foram enviados para o servidor',
            'Certifique-se de que a estrutura de diretórios está correta'
        ]
    );
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Bootstrap Laravel and handle the request...
// Otimizado: captura apenas erros críticos, o Laravel gerencia o resto
try {
    /** @var Application $app */
    $app = require_once $bootstrapPath;
    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle($request = Request::capture());
    $response->send();
    $kernel->terminate($request, $response);
} catch (PDOException $e) {
    // Erro de conexão com banco de dados
    $errorMessage = $e->getMessage();
    $solutions = [];
    
    if (strpos($errorMessage, 'Access denied') !== false || strpos($errorMessage, '1045') !== false) {
        showInitializationError(
            'Erro de Autenticação no Banco de Dados',
            'Não foi possível conectar ao banco de dados. Credenciais incorretas.',
            $errorMessage,
            [
                'Verifique no arquivo .env se DB_USERNAME e DB_PASSWORD estão corretos',
                'Confirme no painel da Hostinger (MySQL Databases) se o usuário e senha estão corretos',
                'Verifique se o usuário tem permissão para acessar o banco de dados',
                'Teste a conexão diretamente: <span class="code">mysql -u SEU_USUARIO -p</span>'
            ]
        );
    } elseif (strpos($errorMessage, 'Unknown database') !== false || strpos($errorMessage, '1049') !== false) {
        showInitializationError(
            'Banco de Dados não Encontrado',
            'O banco de dados especificado não existe.',
            $errorMessage,
            [
                'Verifique no arquivo .env se DB_DATABASE está com o nome correto',
                'Confirme no painel da Hostinger se o banco de dados foi criado',
                'Crie o banco de dados no painel da Hostinger se ainda não existe',
                'O nome do banco geralmente tem o formato: <span class="code">u123456789_nome_banco</span>'
            ]
        );
    } elseif (strpos($errorMessage, 'Connection refused') !== false || strpos($errorMessage, '2002') !== false) {
        showInitializationError(
            'Não foi possível conectar ao servidor MySQL',
            'O servidor MySQL não está acessível no host especificado.',
            $errorMessage,
            [
                'Verifique no arquivo .env se DB_HOST está correto (geralmente é "localhost" na Hostinger)',
                'Confirme no painel da Hostinger qual é o host do MySQL',
                'Tente alterar DB_HOST para: <span class="code">localhost</span> ou <span class="code">127.0.0.1</span>',
                'Verifique se a porta DB_PORT está correta (geralmente 3306)'
            ]
        );
    } else {
        showInitializationError(
            'Erro de Conexão com Banco de Dados',
            'Não foi possível estabelecer conexão com o banco de dados.',
            $errorMessage,
            [
                'Verifique todas as configurações do banco no arquivo .env',
                'Confirme no painel da Hostinger se o banco está ativo',
                'Teste a conexão manualmente usando as credenciais',
                'Verifique os logs do MySQL no painel da Hostinger'
            ]
        );
    }
} catch (Exception $e) {
    // Outros erros
    $errorMessage = $e->getMessage();
    $file = $e->getFile();
    $line = $e->getLine();
    
    showInitializationError(
        'Erro ao Inicializar Aplicação',
        'Ocorreu um erro ao tentar inicializar o Laravel.',
        "Erro: {$errorMessage}\nArquivo: {$file}\nLinha: {$line}",
        [
            'Verifique os logs em: <span class="code">storage/logs/laravel.log</span>',
            'Execute: <span class="code">php artisan config:clear</span>',
            'Execute: <span class="code">php artisan cache:clear</span>',
            'Verifique se todas as permissões estão corretas (storage/ e bootstrap/cache/ devem ser 775)',
            'Se o erro persistir, contate o suporte com os detalhes do erro'
        ]
    );
}

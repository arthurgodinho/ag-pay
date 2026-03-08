<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\QueryException;
use PDOException;
use Illuminate\Support\Facades\Log;
use Throwable;
use Illuminate\Http\Request;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        // Trata erros de banco de dados
        if ($e instanceof QueryException || $e instanceof PDOException) {
            return $this->handleDatabaseException($request, $e);
        }

        // Chama o handler padrão para outros erros
        return parent::render($request, $e);
    }

    /**
     * Handle database exceptions with helpful error messages
     */
    protected function handleDatabaseException($request, $e)
    {
        $errorMessage = $e->getMessage();
        $title = 'Erro de Banco de Dados';
        $message = 'Ocorreu um erro ao conectar ou consultar o banco de dados.';
        $solutions = [];

        // Analisa o código de erro
        if (preg_match('/\(SQLSTATE\[(\d+)\]/', $errorMessage, $matches)) {
            $sqlState = $matches[1] ?? null;
            
            switch ($sqlState) {
                case 'HY000':
                case '1045':
                    if (strpos($errorMessage, 'Access denied') !== false) {
                        $title = 'Erro de Autenticação no Banco de Dados';
                        $message = 'As credenciais do banco de dados estão incorretas.';
                        $solutions = [
                            'Verifique no arquivo .env se DB_USERNAME e DB_PASSWORD estão corretos',
                            'Confirme no painel da Hostinger (MySQL Databases) as credenciais',
                            'Certifique-se de que o usuário tem permissão para acessar o banco',
                        ];
                    }
                    break;
                    
                case '42S02':
                case '42S22':
                    $title = 'Tabela ou Coluna não Encontrada';
                    $message = 'Uma tabela ou coluna necessária não existe no banco de dados.';
                    $solutions = [
                        'Execute as migrações: php artisan migrate --force',
                        'Verifique se todas as migrações foram executadas corretamente',
                        'Confira se há erros nas migrações',
                    ];
                    break;
                    
                case '42S01':
                    $title = 'Tabela já Existe';
                    $message = 'A tabela que você está tentando criar já existe no banco.';
                    $solutions = [
                        'Execute: php artisan migrate:fresh --force (CUIDADO: apaga todos os dados)',
                        'Ou remova as tabelas manualmente e execute as migrações novamente',
                    ];
                    break;
            }
        }

        // Erros específicos do MySQL
        if (strpos($errorMessage, 'Unknown database') !== false || preg_match('/1049/', $errorMessage)) {
            $title = 'Banco de Dados não Encontrado';
            $message = 'O banco de dados especificado não existe.';
            $solutions = [
                'Verifique no arquivo .env se DB_DATABASE está com o nome correto',
                'Confirme no painel da Hostinger se o banco foi criado',
                'Crie o banco de dados no painel da Hostinger',
            ];
        } elseif (strpos($errorMessage, 'Connection refused') !== false || preg_match('/2002/', $errorMessage)) {
            $title = 'Não foi possível conectar ao MySQL';
            $message = 'O servidor MySQL não está acessível.';
            $solutions = [
                'Verifique no arquivo .env se DB_HOST está correto (geralmente "localhost")',
                'Confirme no painel da Hostinger qual é o host do MySQL',
                'Tente alterar DB_HOST para: localhost ou 127.0.0.1',
            ];
        } elseif (strpos($errorMessage, 'No connection could be made') !== false) {
            $title = 'Conexão Recusada';
            $message = 'Não foi possível estabelecer conexão com o servidor MySQL.';
            $solutions = [
                'Verifique se o MySQL está rodando',
                'Confirme se a porta DB_PORT está correta (geralmente 3306)',
                'Verifique se há firewall bloqueando a conexão',
            ];
        }

        // Se não há soluções específicas, usa as genéricas
        if (empty($solutions)) {
            $solutions = [
                'Verifique todas as configurações do banco no arquivo .env',
                'Confirme no painel da Hostinger se o banco está ativo',
                'Execute: php artisan config:clear && php artisan cache:clear',
                'Verifique os logs: storage/logs/laravel.log',
            ];
        }

        // Log do erro
        Log::error('Database Exception: ' . $errorMessage, [
            'exception' => $e,
            'sql_state' => $sqlState ?? null,
        ]);

        // Retorna resposta JSON para API ou HTML para web
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'error' => true,
                'title' => $title,
                'message' => $message,
                'solutions' => $solutions,
                'details' => config('app.debug') ? $errorMessage : null,
            ], 500);
        }

        // Retorna view de erro personalizada
        return response()->view('errors.database', [
            'title' => $title,
            'message' => $message,
            'error' => $errorMessage,
            'solutions' => $solutions,
            'debug' => config('app.debug'),
        ], 500);
    }
}

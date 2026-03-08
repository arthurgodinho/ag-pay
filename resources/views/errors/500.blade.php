<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erro do Servidor - NexusPay</title>
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
            text-align: center;
        }
        .solutions {
            margin-top: 30px;
        }
        .solutions h2 {
            color: #10b981;
            font-size: 20px;
            margin-bottom: 15px;
        }
        .solutions ul {
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
        .button {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 24px;
            background: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: background 0.3s;
            text-align: center;
        }
        .button:hover {
            background: #2563eb;
        }
        .button-container {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-icon">⚠️</div>
        <h1>Erro 500 - Erro Interno do Servidor</h1>
        <div class="message">
            Ocorreu um erro ao processar sua requisição. Por favor, tente novamente mais tarde.
        </div>
        
        <div class="solutions">
            <h2>🔧 O que você pode fazer:</h2>
            <ul>
                <li>Verifique os logs em: <span class="code">storage/logs/laravel.log</span></li>
                <li>Limpe o cache: <span class="code">php artisan config:clear && php artisan cache:clear</span></li>
                <li>Verifique se todas as configurações estão corretas no arquivo .env</li>
                <li>Confirme se o banco de dados está acessível</li>
                <li>Verifique as permissões das pastas: <span class="code">storage/</span> e <span class="code">bootstrap/cache/</span></li>
            </ul>
        </div>
        
        <div class="button-container">
            <a href="/" class="button">← Voltar para a página inicial</a>
        </div>
    </div>
</body>
</html>


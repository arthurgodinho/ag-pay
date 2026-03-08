<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso Negado - NexusPay</title>
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
            max-width: 600px;
            width: 100%;
            background: #1a1f2e;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
            border: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }
        .error-icon {
            font-size: 100px;
            margin-bottom: 20px;
        }
        h1 {
            color: #ef4444;
            font-size: 48px;
            margin-bottom: 10px;
        }
        h2 {
            color: #fff;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .message {
            color: #9ca3af;
            font-size: 16px;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: background 0.3s;
        }
        .button:hover {
            background: #2563eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-icon">🚫</div>
        <h1>403</h1>
        <h2>Acesso Negado</h2>
        <div class="message">
            Você não tem permissão para acessar esta página.
        </div>
        <a href="/" class="button">← Voltar para a página inicial</a>
    </div>
</body>
</html>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site em Manutenção - NexusPay</title>
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
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        h1 {
            color: #fbbf24;
            font-size: 32px;
            margin-bottom: 10px;
        }
        .message {
            color: #9ca3af;
            font-size: 16px;
            margin-bottom: 30px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-icon">🔧</div>
        <h1>Site em Manutenção</h1>
        <div class="message">
            Estamos realizando uma manutenção rápida. Voltaremos em breve!
        </div>
        <div class="message">
            Por favor, tente novamente em alguns minutos.
        </div>
    </div>
</body>
</html>


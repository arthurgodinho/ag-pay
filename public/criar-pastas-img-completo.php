<?php
/**
 * Script para criar todas as pastas IMG necessárias no public_html
 * Execute este arquivo uma vez para garantir que todas as pastas existam
 */

// Carrega o autoload do Laravel
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<!DOCTYPE html>
<html lang='pt-BR'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Criar Pastas IMG</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0B0E14 0%, #151A23 100%);
            color: #fff;
            padding: 2rem;
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(21, 26, 35, 0.9);
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        }
        h1 {
            color: #00B2FF;
            margin-bottom: 1.5rem;
            font-size: 2rem;
        }
        .success {
            color: #00D9AC;
            padding: 0.5rem;
            margin: 0.5rem 0;
            background: rgba(0, 217, 172, 0.1);
            border-left: 3px solid #00D9AC;
            border-radius: 0.25rem;
        }
        .error {
            color: #FF6B6B;
            padding: 0.5rem;
            margin: 0.5rem 0;
            background: rgba(255, 107, 107, 0.1);
            border-left: 3px solid #FF6B6B;
            border-radius: 0.25rem;
        }
        .info {
            color: #00B2FF;
            padding: 0.5rem;
            margin: 0.5rem 0;
            background: rgba(0, 178, 255, 0.1);
            border-left: 3px solid #00B2FF;
            border-radius: 0.25rem;
        }
        code {
            background: rgba(0, 0, 0, 0.3);
            padding: 0.2rem 0.4rem;
            border-radius: 0.25rem;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
        }
        .summary {
            margin-top: 2rem;
            padding: 1rem;
            background: rgba(0, 178, 255, 0.1);
            border-radius: 0.5rem;
            border: 1px solid rgba(0, 178, 255, 0.3);
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>📁 Criar Estrutura de Pastas IMG</h1>";

$basePath = __DIR__ . '/IMG';
$pathsToCreate = [
    $basePath . '/landing/logo',
    $basePath . '/landing/favicon',
    $basePath . '/landing/hero',
    $basePath . '/kyc',
    $basePath . '/profile',
    $basePath . '/uploads',
    $basePath . '/temp',
];

$created = 0;
$alreadyExists = 0;
$errors = 0;

foreach ($pathsToCreate as $path) {
    if (!is_dir($path)) {
        if (mkdir($path, 0755, true)) {
            echo "<p class='success'>✅ Diretório criado: <code>" . htmlspecialchars(str_replace(__DIR__, 'public', $path)) . "</code></p>";
            $created++;
        } else {
            echo "<p class='error'>❌ Erro ao criar diretório: <code>" . htmlspecialchars(str_replace(__DIR__, 'public', $path)) . "</code></p>";
            $errors++;
        }
    } else {
        echo "<p class='info'>ℹ️ Diretório já existe: <code>" . htmlspecialchars(str_replace(__DIR__, 'public', $path)) . "</code></p>";
        $alreadyExists++;
    }
}

// Verifica e cria pastas dinâmicas para KYC (por usuário, será criado sob demanda)
echo "<p class='info'>ℹ️ Pastas de KYC por usuário serão criadas automaticamente quando necessário.</p>";

echo "<div class='summary'>
    <h2>📊 Resumo:</h2>
    <p>✅ Criados: <strong>{$created}</strong></p>
    <p>ℹ️ Já existiam: <strong>{$alreadyExists}</strong></p>
    <p>❌ Erros: <strong>{$errors}</strong></p>
    " . ($errors === 0 ? "<p style='color: #00D9AC; margin-top: 1rem;'><strong>✅ Todas as pastas estão prontas!</strong></p>" : "<p style='color: #FF6B6B; margin-top: 1rem;'><strong>⚠️ Verifique os erros acima e ajuste as permissões do servidor.</strong></p>") . "
</div>";

echo "    </div>
</body>
</html>";




<?php
/**
 * SCRIPT PARA CORRIGIR APP_KEY E VERIFICAR UPLOAD
 * 
 * Este script:
 * 1. Corrige a APP_KEY no formato correto (base64:...)
 * 2. Verifica configurações de storage
 * 3. Testa criação de diretórios
 */

// Define o caminho do projeto Laravel
$laravelPath = dirname(__DIR__);

// Define o caminho do arquivo .env
$envPath = $laravelPath . DIRECTORY_SEPARATOR . '.env';

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corrigir APP_KEY e Upload - PagueMax</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #0B0E14 0%, #151A23 100%);
            color: #fff;
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: #1A1F2E;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
        }
        h1 {
            color: #00B2FF;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .subtitle {
            color: #94a3b8;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .section {
            background: #0B0E14;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .section h2 {
            color: #00B2FF;
            font-size: 18px;
            margin-bottom: 15px;
        }
        .success {
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid #10b981;
            color: #10b981;
            padding: 12px;
            border-radius: 8px;
            margin: 10px 0;
        }
        .error {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid #ef4444;
            color: #ef4444;
            padding: 12px;
            border-radius: 8px;
            margin: 10px 0;
        }
        .warning {
            background: rgba(251, 191, 36, 0.2);
            border: 1px solid #fbbf24;
            color: #fbbf24;
            padding: 12px;
            border-radius: 8px;
            margin: 10px 0;
        }
        .info {
            background: rgba(59, 130, 246, 0.2);
            border: 1px solid #3b82f6;
            color: #3b82f6;
            padding: 12px;
            border-radius: 8px;
            margin: 10px 0;
        }
        code {
            background: rgba(0,0,0,0.3);
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
        }
        .btn {
            background: #00B2FF;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            margin: 5px;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #0099CC;
        }
        .btn-danger {
            background: #ef4444;
        }
        .btn-danger:hover {
            background: #dc2626;
        }
        ul {
            list-style: none;
            padding-left: 0;
        }
        li {
            padding: 5px 0;
        }
        li:before {
            content: "✓ ";
            color: #10b981;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Corrigir APP_KEY e Verificar Upload</h1>
        <p class="subtitle">Script para corrigir problemas de criptografia e upload de arquivos</p>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'fix_app_key') {
                echo '<div class="section">';
                echo '<h2>🔑 Corrigindo APP_KEY...</h2>';

                if (!file_exists($envPath)) {
                    echo '<div class="error">❌ Arquivo .env não encontrado em: ' . htmlspecialchars($envPath) . '</div>';
                } else {
                    // Lê o arquivo .env
                    $envContent = file_get_contents($envPath);
                    
                    // Gera uma nova APP_KEY no formato correto
                    $randomBytes = random_bytes(32); // 32 bytes para AES-256-CBC
                    $newAppKey = 'base64:' . base64_encode($randomBytes);
                    
                    // Verifica se já existe APP_KEY
                    if (preg_match('/^APP_KEY=.*/m', $envContent)) {
                        $envContent = preg_replace('/^APP_KEY=.*/m', 'APP_KEY=' . $newAppKey, $envContent);
                        echo '<div class="success">✅ APP_KEY atualizada no arquivo .env</div>';
                    } else {
                        // Adiciona APP_KEY após APP_ENV
                        $envContent = preg_replace('/(APP_ENV=.*)/', "$1\nAPP_KEY=$newAppKey", $envContent);
                        echo '<div class="success">✅ APP_KEY adicionada ao arquivo .env</div>';
                    }
                    
                    // Salva o arquivo
                    if (file_put_contents($envPath, $envContent)) {
                        echo '<div class="info">📝 Nova APP_KEY: <code>' . substr($newAppKey, 0, 40) . '...</code></div>';
                        echo '<div class="warning">⚠️ IMPORTANTE: Limpe o cache de configuração após isso!</div>';
                    } else {
                        echo '<div class="error">❌ Erro ao salvar o arquivo .env. Verifique as permissões.</div>';
                    }
                }
                echo '</div>';
            }

            if ($action === 'check_storage') {
                echo '<div class="section">';
                echo '<h2>📁 Verificando Storage...</h2>';

                $storagePublicPath = $laravelPath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public';
                $publicStorageLink = $laravelPath . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'storage';
                $landingLogoPath = $storagePublicPath . DIRECTORY_SEPARATOR . 'landing' . DIRECTORY_SEPARATOR . 'logo';
                $landingFaviconPath = $storagePublicPath . DIRECTORY_SEPARATOR . 'landing' . DIRECTORY_SEPARATOR . 'favicon';

                // Verifica diretório storage/app/public
                if (!is_dir($storagePublicPath)) {
                    echo '<div class="error">❌ Diretório storage/app/public não existe</div>';
                    if (mkdir($storagePublicPath, 0755, true)) {
                        echo '<div class="success">✅ Diretório storage/app/public criado</div>';
                    } else {
                        echo '<div class="error">❌ Não foi possível criar o diretório</div>';
                    }
                } else {
                    echo '<div class="success">✅ Diretório storage/app/public existe</div>';
                }

                // Verifica link simbólico
                if (is_link($publicStorageLink)) {
                    echo '<div class="success">✅ Link simbólico public/storage existe</div>';
                } else {
                    echo '<div class="warning">⚠️ Link simbólico public/storage não existe</div>';
                    echo '<div class="info">💡 Execute: <code>php artisan storage:link</code></div>';
                }

                // Cria diretórios de landing
                if (!is_dir($landingLogoPath)) {
                    if (mkdir($landingLogoPath, 0755, true)) {
                        echo '<div class="success">✅ Diretório landing/logo criado</div>';
                    }
                } else {
                    echo '<div class="success">✅ Diretório landing/logo existe</div>';
                }

                if (!is_dir($landingFaviconPath)) {
                    if (mkdir($landingFaviconPath, 0755, true)) {
                        echo '<div class="success">✅ Diretório landing/favicon criado</div>';
                    }
                } else {
                    echo '<div class="success">✅ Diretório landing/favicon existe</div>';
                }

                // Verifica permissões
                if (is_writable($storagePublicPath)) {
                    echo '<div class="success">✅ Diretório storage/app/public é gravável</div>';
                } else {
                    echo '<div class="error">❌ Diretório storage/app/public NÃO é gravável</div>';
                    echo '<div class="info">💡 Ajuste as permissões: <code>chmod -R 775 storage</code></div>';
                }

                echo '</div>';
            }
        }
        ?>

        <div class="section">
            <h2>📋 Ações Disponíveis</h2>
            <form method="POST" style="margin-top: 15px;">
                <input type="hidden" name="action" value="fix_app_key">
                <button type="submit" class="btn">🔑 Corrigir APP_KEY</button>
            </form>
            <p style="color: #94a3b8; margin-top: 10px; font-size: 13px;">
                Gera uma nova APP_KEY no formato correto (base64:...) para AES-256-CBC
            </p>
        </div>

        <div class="section">
            <h2>📁 Verificar Storage</h2>
            <form method="POST" style="margin-top: 15px;">
                <input type="hidden" name="action" value="check_storage">
                <button type="submit" class="btn">🔍 Verificar Diretórios</button>
            </form>
            <p style="color: #94a3b8; margin-top: 10px; font-size: 13px;">
                Verifica e cria os diretórios necessários para upload de arquivos
            </p>
        </div>

        <div class="section">
            <h2>⚠️ Próximos Passos</h2>
            <p style="color: #94a3b8; margin-top: 10px;">
                Após corrigir a APP_KEY, execute os seguintes comandos:
            </p>
            <ul style="margin-top: 10px;">
                <li>Limpar cache: <code>php artisan config:clear</code></li>
                <li>Limpar cache de aplicação: <code>php artisan cache:clear</code></li>
                <li>Criar link do storage: <code>php artisan storage:link</code></li>
                <li>Recarregar configuração: <code>php artisan config:cache</code></li>
            </ul>
        </div>

        <div class="warning" style="margin-top: 20px;">
            <strong>⚠️ IMPORTANTE:</strong> Delete este arquivo após usar por questões de segurança!
        </div>
    </div>
</body>
</html>




<!DOCTYPE html>
<html>
<head>
    <title>Seu Produto Digital</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #2d3748;">Olá, {{ $transaction->payer_name ?? 'Cliente' }}!</h2>
        
        <p>Obrigado por sua compra. O pagamento foi confirmado e seu produto já está disponível para download.</p>
        
        <div style="background-color: #f7fafc; border-left: 4px solid #48bb78; padding: 15px; margin: 20px 0;">
            <h3 style="margin-top: 0; color: #2d3748;">{{ $product->name }}</h3>
            <p style="margin-bottom: 0;">
                <a href="{{ $product->download_url }}" style="background-color: #48bb78; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Baixar Agora</a>
            </p>
        </div>
        
        <p>Se o botão acima não funcionar, copie e cole o seguinte link no seu navegador:</p>
        <p style="word-break: break-all; color: #718096; font-size: 14px;">{{ $product->download_url }}</p>
        
        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 30px 0;">
        
        <p style="font-size: 12px; color: #718096;">Este é um e-mail automático. Por favor, não responda.</p>
    </div>
</body>
</html>

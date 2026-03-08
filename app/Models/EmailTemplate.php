<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'type',
        'subject',
        'body_html',
        'body_text',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Busca template por tipo
     */
    public static function getByType($type)
    {
        return self::where('type', $type)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Tipos de template disponíveis
     */
    public static function getAvailableTypes()
    {
        return [
            'user_registered' => 'Usuário Cadastrado',
            'user_registration_pending' => 'Cadastro em Análise',
            'user_approved' => 'Conta Aprovada',
            'user_incomplete_registration' => 'Cadastro Incompleto',
            'payment_received' => 'Pagamento Recebido',
            'payment_sent' => 'Pagamento Enviado',
            'payment_pending' => 'Pagamento Pendente',
            'payment_failed' => 'Pagamento Falhou',
            'checkout_sale' => 'Nova Venda no Checkout',
            'abandoned_cart' => 'Carrinho Abandonado',
            'product_offer' => 'Oferta de Produto',
            'system_news' => 'Notícias do Sistema',
        ];
    }

    /**
     * Renderiza o template com variáveis
     */
    public function render(array $variables = [])
    {
        $html = $this->body_html;
        $text = $this->body_text ?? strip_tags($html);
        $subject = $this->subject;

        foreach ($variables as $key => $value) {
            $html = str_replace('{{' . $key . '}}', $value, $html);
            $text = str_replace('{{' . $key . '}}', $value, $text);
            $subject = str_replace('{{' . $key . '}}', $value, $subject);
        }
        
        // Se o HTML não contém o template base (DOCTYPE), envolve com template base moderno
        if (strpos($html, '<!DOCTYPE') === false && strpos($html, '<html') === false) {
            $html = \App\Helpers\EmailTemplateHelper::getBaseTemplate($html);
        }
        
        // Substitui {{subject}} no template base se existir
        $html = str_replace('{{subject}}', $subject, $html);

        return [
            'subject' => $subject,
            'html' => $html,
            'text' => $text,
        ];
    }
}


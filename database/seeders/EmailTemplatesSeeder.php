<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;

class EmailTemplatesSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'type' => 'user_registered',
                'subject' => 'Bem-vindo ao {{app_name}}!',
                'body_html' => $this->getTemplateHtml('user_registered'),
                'body_text' => $this->getTemplateText('user_registered'),
                'is_active' => true,
            ],
            [
                'type' => 'user_registration_pending',
                'subject' => 'Seu cadastro está em análise - {{app_name}}',
                'body_html' => $this->getTemplateHtml('user_registration_pending'),
                'body_text' => $this->getTemplateText('user_registration_pending'),
                'is_active' => true,
            ],
            [
                'type' => 'user_approved',
                'subject' => 'Sua conta foi aprovada! - {{app_name}}',
                'body_html' => $this->getTemplateHtml('user_approved'),
                'body_text' => $this->getTemplateText('user_approved'),
                'is_active' => true,
            ],
            [
                'type' => 'user_incomplete_registration',
                'subject' => 'Complete seu cadastro - {{app_name}}',
                'body_html' => $this->getTemplateHtml('user_incomplete_registration'),
                'body_text' => $this->getTemplateText('user_incomplete_registration'),
                'is_active' => true,
            ],
            [
                'type' => 'payment_received',
                'subject' => 'Você recebeu um pagamento! - {{app_name}}',
                'body_html' => $this->getTemplateHtml('payment_received'),
                'body_text' => $this->getTemplateText('payment_received'),
                'is_active' => true,
            ],
            [
                'type' => 'payment_sent',
                'subject' => 'Pagamento enviado com sucesso - {{app_name}}',
                'body_html' => $this->getTemplateHtml('payment_sent'),
                'body_text' => $this->getTemplateText('payment_sent'),
                'is_active' => true,
            ],
            [
                'type' => 'payment_pending',
                'subject' => 'Pagamento pendente - {{app_name}}',
                'body_html' => $this->getTemplateHtml('payment_pending'),
                'body_text' => $this->getTemplateText('payment_pending'),
                'is_active' => true,
            ],
            [
                'type' => 'payment_failed',
                'subject' => 'Falha no processamento do pagamento - {{app_name}}',
                'body_html' => $this->getTemplateHtml('payment_failed'),
                'body_text' => $this->getTemplateText('payment_failed'),
                'is_active' => true,
            ],
            [
                'type' => 'checkout_sale',
                'subject' => 'Nova venda realizada! - {{app_name}}',
                'body_html' => $this->getTemplateHtml('checkout_sale'),
                'body_text' => $this->getTemplateText('checkout_sale'),
                'is_active' => true,
            ],
            [
                'type' => 'abandoned_cart',
                'subject' => 'Você esqueceu algo no seu carrinho? - {{app_name}}',
                'body_html' => $this->getTemplateHtml('abandoned_cart'),
                'body_text' => $this->getTemplateText('abandoned_cart'),
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['type' => $template['type']],
                $template
            );
        }
    }

    private function getTemplateHtml($type)
    {
        $templates = [
            'user_registered' => '
            <h1 style="color: #212529; font-size: 28px; font-weight: 700; margin: 0 0 20px; text-align: center;">Bem-vindo ao {{app_name}}! 🎉</h1>
            <p style="color: #495057; font-size: 16px; margin: 0 0 20px;">Olá <strong style="color: #212529;">{{user_name}}</strong>,</p>
            <p style="color: #495057; font-size: 16px; margin: 0 0 20px;">É com grande prazer que te damos as boas-vindas ao <strong>{{app_name}}</strong>!</p>
            <p style="color: #495057; font-size: 16px; margin: 0 0 20px;">Seu cadastro foi realizado com sucesso em <strong>{{register_date}}</strong>.</p>
            <p style="color: #495057; font-size: 16px; margin: 0 0 30px;">Agora você pode começar a utilizar todas as funcionalidades da nossa plataforma de pagamentos.</p>
            ' . \App\Helpers\EmailTemplateHelper::getButton('Acessar Minha Conta', '{{app_url}}/login', '#00B2FF') . '
            <p style="color: #6c757d; font-size: 14px; margin: 30px 0 0; text-align: center;">Se tiver alguma dúvida, nossa equipe está pronta para ajudar!</p>
            ',

            'user_registration_pending' => '
            <h1 style="color: #212529; font-size: 28px; font-weight: 700; margin: 0 0 20px; text-align: center;">Cadastro em Análise ⏳</h1>
            <p style="color: #495057; font-size: 16px; margin: 0 0 20px;">Olá <strong style="color: #212529;">{{user_name}}</strong>,</p>
            <p style="color: #495057; font-size: 16px; margin: 0 0 20px;">Recebemos seu cadastro no <strong>{{app_name}}</strong> e ele está sendo analisado pela nossa equipe de segurança.</p>
            ' . \App\Helpers\EmailTemplateHelper::getInfoCard('
                <p style="margin: 0; color: #495057; font-size: 15px;"><strong style="color: #ff9800;">⏱️ Tempo de análise:</strong> Este processo geralmente leva até 24 horas úteis.</p>
                <p style="margin: 10px 0 0; color: #495057; font-size: 15px;"><strong style="color: #ff9800;">📧 Notificação:</strong> Assim que sua conta for aprovada, você receberá um email de confirmação.</p>
            ', '#ff9800') . '
            <p style="color: #495057; font-size: 16px; margin: 20px 0 0;">Obrigado pela sua paciência e por escolher o {{app_name}}!</p>
            ',

            'user_approved' => '
            <h1 style="color: #212529; font-size: 28px; font-weight: 700; margin: 0 0 20px; text-align: center;">Conta Aprovada! 🎉</h1>
            <p style="color: #495057; font-size: 16px; margin: 0 0 20px;">Olá <strong style="color: #212529;">{{user_name}}</strong>,</p>
            <p style="color: #495057; font-size: 16px; margin: 0 0 20px;">Ótimas notícias! Sua conta no <strong>{{app_name}}</strong> foi aprovada em <strong>{{approval_date}}</strong>.</p>
            ' . \App\Helpers\EmailTemplateHelper::getInfoCard('
                <p style="margin: 0; color: #495057; font-size: 15px;">✅ <strong style="color: #28a745;">Sua conta está ativa!</strong></p>
                <p style="margin: 10px 0 0; color: #495057; font-size: 15px;">Agora você pode acessar todas as funcionalidades da plataforma e começar a usar nossos serviços de pagamento.</p>
            ', '#28a745') . '
            ' . \App\Helpers\EmailTemplateHelper::getButton('Acessar Minha Conta', '{{login_url}}', '#28a745') . '
            <p style="color: #6c757d; font-size: 14px; margin: 30px 0 0; text-align: center;">Bem-vindo ao {{app_name}}!</p>
            ',

            'user_incomplete_registration' => '
            <h1 style="color: #212529; font-size: 28px; font-weight: 700; margin: 0 0 20px; text-align: center;">Complete seu Cadastro 📝</h1>
            <p style="color: #495057; font-size: 16px; margin: 0 0 20px;">Olá <strong style="color: #212529;">{{user_name}}</strong>,</p>
            <p style="color: #495057; font-size: 16px; margin: 0 0 20px;">Notamos que você iniciou seu cadastro no <strong>{{app_name}}</strong>, mas não o finalizou.</p>
            ' . \App\Helpers\EmailTemplateHelper::getInfoCard('
                <p style="margin: 0; color: #495057; font-size: 15px;">⏰ <strong style="color: #ff9800;">Complete agora</strong> e comece a usar todas as funcionalidades da plataforma!</p>
                <p style="margin: 10px 0 0; color: #495057; font-size: 15px;">O processo leva apenas alguns minutos e você terá acesso completo ao sistema.</p>
            ', '#ff9800') . '
            ' . \App\Helpers\EmailTemplateHelper::getButton('Finalizar Cadastro', '{{complete_registration_url}}', '#ff9800') . '
            <p style="color: #6c757d; font-size: 14px; margin: 30px 0 0; text-align: center;">Se tiver dúvidas, nossa equipe está pronta para ajudar!</p>
            ',

            'payment_received' => '
            <h1 style="color: #212529; font-size: 28px; font-weight: 700; margin: 0 0 20px; text-align: center;">Pagamento Recebido! 💰</h1>
            <p style="color: #495057; font-size: 16px; margin: 0 0 20px;">Olá <strong style="color: #212529;">{{user_name}}</strong>,</p>
            <p style="color: #495057; font-size: 16px; margin: 0 0 20px;">Você recebeu um novo pagamento!</p>
            ' . \App\Helpers\EmailTemplateHelper::getInfoCard('
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="padding: 8px 0; color: #495057; font-size: 15px;"><strong style="color: #212529;">💰 Valor:</strong></td>
                        <td style="padding: 8px 0; color: #28a745; font-size: 18px; font-weight: 700; text-align: right;">{{amount}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #495057; font-size: 15px;"><strong style="color: #212529;">💳 Método:</strong></td>
                        <td style="padding: 8px 0; color: #495057; font-size: 15px; text-align: right;">{{payment_method}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #495057; font-size: 15px;"><strong style="color: #212529;">🆔 ID:</strong></td>
                        <td style="padding: 8px 0; color: #6c757d; font-size: 13px; text-align: right; font-family: monospace;">{{transaction_id}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #495057; font-size: 15px;"><strong style="color: #212529;">📅 Data:</strong></td>
                        <td style="padding: 8px 0; color: #495057; font-size: 15px; text-align: right;">{{transaction_date}}</td>
                    </tr>
                </table>
            ', '#28a745') . '
            <p style="color: #28a745; font-size: 16px; font-weight: 600; margin: 20px 0; text-align: center;">✅ O valor já está disponível na sua conta!</p>
            ' . \App\Helpers\EmailTemplateHelper::getButton('Ver Detalhes', '{{app_url}}/dashboard', '#28a745') . '
            ',

            'payment_sent' => '
            <h1 style="color: #212529; font-size: 28px; font-weight: 700; margin: 0 0 20px; text-align: center;">Pagamento Enviado ✅</h1>
            <p style="color: #495057; font-size: 16px; margin: 0 0 20px;">Olá <strong style="color: #212529;">{{user_name}}</strong>,</p>
            <p style="color: #495057; font-size: 16px; margin: 0 0 20px;">Seu pagamento foi processado e enviado com sucesso!</p>
            ' . \App\Helpers\EmailTemplateHelper::getInfoCard('
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="padding: 8px 0; color: #495057; font-size: 15px;"><strong style="color: #212529;">💰 Valor:</strong></td>
                        <td style="padding: 8px 0; color: #495057; font-size: 18px; font-weight: 700; text-align: right;">{{amount}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #495057; font-size: 15px;"><strong style="color: #212529;">💳 Método:</strong></td>
                        <td style="padding: 8px 0; color: #495057; font-size: 15px; text-align: right;">{{payment_method}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #495057; font-size: 15px;"><strong style="color: #212529;">🆔 ID:</strong></td>
                        <td style="padding: 8px 0; color: #6c757d; font-size: 13px; text-align: right; font-family: monospace;">{{transaction_id}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #495057; font-size: 15px;"><strong style="color: #212529;">📅 Data:</strong></td>
                        <td style="padding: 8px 0; color: #495057; font-size: 15px; text-align: right;">{{transaction_date}}</td>
                    </tr>
                </table>
            ', '#2196f3') . '
            <p style="color: #495057; font-size: 16px; margin: 20px 0; text-align: center;">O pagamento foi debitado da sua conta.</p>
            ' . \App\Helpers\EmailTemplateHelper::getButton('Ver Detalhes', '{{app_url}}/dashboard', '#2196f3') . '
            ',

            'payment_pending' => '
            <h1 style="color: #212529; font-size: 28px; font-weight: 700; margin: 0 0 20px; text-align: center;">Pagamento Pendente ⏳</h1>
            <p style="color: #495057; font-size: 16px; margin: 0 0 20px;">Olá <strong style="color: #212529;">{{user_name}}</strong>,</p>
            <p style="color: #495057; font-size: 16px; margin: 0 0 20px;">Você tem um pagamento aguardando confirmação.</p>
            ' . \App\Helpers\EmailTemplateHelper::getInfoCard('
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="padding: 8px 0; color: #495057; font-size: 15px;"><strong style="color: #212529;">💰 Valor:</strong></td>
                        <td style="padding: 8px 0; color: #495057; font-size: 18px; font-weight: 700; text-align: right;">{{amount}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #495057; font-size: 15px;"><strong style="color: #212529;">💳 Método:</strong></td>
                        <td style="padding: 8px 0; color: #495057; font-size: 15px; text-align: right;">{{payment_method}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #495057; font-size: 15px;"><strong style="color: #212529;">🆔 ID:</strong></td>
                        <td style="padding: 8px 0; color: #6c757d; font-size: 13px; text-align: right; font-family: monospace;">{{transaction_id}}</td>
                    </tr>
                </table>
            ', '#ff9800') . '
            <p style="color: #495057; font-size: 16px; margin: 20px 0; text-align: center;">Para finalizar o pagamento, clique no botão abaixo:</p>
            ' . \App\Helpers\EmailTemplateHelper::getButton('Finalizar Pagamento', '{{payment_url}}', '#ff9800') . '
            ',

            'payment_failed' => '
            <h1 style="color: #212529; font-size: 28px; font-weight: 700; margin: 0 0 20px; text-align: center;">Falha no Pagamento ⚠️</h1>
            <p style="color: #495057; font-size: 16px; margin: 0 0 20px;">Olá <strong style="color: #212529;">{{user_name}}</strong>,</p>
            <p style="color: #495057; font-size: 16px; margin: 0 0 20px;">Infelizmente, ocorreu uma falha no processamento do seu pagamento.</p>
            ' . \App\Helpers\EmailTemplateHelper::getInfoCard('
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="padding: 8px 0; color: #495057; font-size: 15px;"><strong style="color: #212529;">💰 Valor:</strong></td>
                        <td style="padding: 8px 0; color: #495057; font-size: 18px; font-weight: 700; text-align: right;">{{amount}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #495057; font-size: 15px;"><strong style="color: #212529;">🆔 ID:</strong></td>
                        <td style="padding: 8px 0; color: #6c757d; font-size: 13px; text-align: right; font-family: monospace;">{{transaction_id}}</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding: 12px 0 0; border-top: 1px solid #dee2e6;">
                            <p style="margin: 0; color: #dc3545; font-size: 14px;"><strong>❌ Motivo:</strong> {{error_message}}</p>
                        </td>
                    </tr>
                </table>
            ', '#dc3545') . '
            <p style="color: #495057; font-size: 16px; margin: 20px 0; text-align: center;">Por favor, verifique os dados e tente novamente.</p>
            ' . \App\Helpers\EmailTemplateHelper::getButton('Tentar Novamente', '{{app_url}}/dashboard', '#dc3545') . '
            <p style="color: #6c757d; font-size: 14px; margin: 30px 0 0; text-align: center;">Se o problema persistir, entre em contato com nosso suporte.</p>
            ',

            'checkout_sale' => '
            <h1 style="color: #212529; font-size: 28px; font-weight: 700; margin: 0 0 20px; text-align: center;">Nova Venda Realizada! 🎉</h1>
            <p style="color: #495057; font-size: 16px; margin: 0 0 20px;">Olá <strong style="color: #212529;">{{user_name}}</strong>,</p>
            <p style="color: #495057; font-size: 16px; margin: 0 0 20px;">Parabéns! Você realizou uma nova venda no seu checkout!</p>
            ' . \App\Helpers\EmailTemplateHelper::getInfoCard('
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="padding: 8px 0; color: #495057; font-size: 15px;"><strong style="color: #212529;">📦 Produto:</strong></td>
                        <td style="padding: 8px 0; color: #495057; font-size: 15px; text-align: right;">{{product_name}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #495057; font-size: 15px;"><strong style="color: #212529;">💰 Valor:</strong></td>
                        <td style="padding: 8px 0; color: #28a745; font-size: 18px; font-weight: 700; text-align: right;">{{amount}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #495057; font-size: 15px;"><strong style="color: #212529;">👤 Cliente:</strong></td>
                        <td style="padding: 8px 0; color: #495057; font-size: 15px; text-align: right;">{{customer_email}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #495057; font-size: 15px;"><strong style="color: #212529;">📅 Data:</strong></td>
                        <td style="padding: 8px 0; color: #495057; font-size: 15px; text-align: right;">{{sale_date}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #495057; font-size: 15px;"><strong style="color: #212529;">🆔 ID:</strong></td>
                        <td style="padding: 8px 0; color: #6c757d; font-size: 13px; text-align: right; font-family: monospace;">#{{sale_id}}</td>
                    </tr>
                </table>
            ', '#28a745') . '
            <p style="color: #28a745; font-size: 16px; font-weight: 600; margin: 20px 0; text-align: center;">✅ O valor já está disponível na sua conta!</p>
            ' . \App\Helpers\EmailTemplateHelper::getButton('Ver Vendas', '{{app_url}}/checkout-panel/sales', '#28a745') . '
            ',

            'abandoned_cart' => '
            <h1 style="color: #212529; font-size: 28px; font-weight: 700; margin: 0 0 20px; text-align: center;">Você esqueceu algo? 🛒</h1>
            <p style="color: #495057; font-size: 16px; margin: 0 0 20px;">Olá <strong style="color: #212529;">{{user_name}}</strong>,</p>
            <p style="color: #495057; font-size: 16px; margin: 0 0 20px;">Notamos que você estava interessado no produto <strong>{{product_name}}</strong>, mas não finalizou a compra.</p>
            ' . \App\Helpers\EmailTemplateHelper::getInfoCard('
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="padding: 8px 0; color: #495057; font-size: 15px;"><strong style="color: #212529;">📦 Produto:</strong></td>
                        <td style="padding: 8px 0; color: #495057; font-size: 15px; text-align: right;">{{product_name}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #495057; font-size: 15px;"><strong style="color: #212529;">💰 Valor:</strong></td>
                        <td style="padding: 8px 0; color: #495057; font-size: 18px; font-weight: 700; text-align: right;">{{amount}}</td>
                    </tr>
                </table>
            ', '#ff9800') . '
            <p style="color: #495057; font-size: 16px; margin: 20px 0; text-align: center;">Complete sua compra agora e aproveite!</p>
            ' . \App\Helpers\EmailTemplateHelper::getButton('Finalizar Compra', '{{checkout_url}}', '#ff9800') . '
            <p style="color: #6c757d; font-size: 14px; margin: 30px 0 0; text-align: center;">⏰ Não perca essa oportunidade!</p>
            ',
        ];

        return $templates[$type] ?? '';
    }

    private function getTemplateText($type)
    {
        // Versão texto simples para clientes de email que não suportam HTML
        $texts = [
            'user_registered' => "Bem-vindo ao {{app_name}}!\n\nOlá {{user_name}},\n\nÉ com grande prazer que te damos as boas-vindas ao {{app_name}}!\n\nSeu cadastro foi realizado com sucesso em {{register_date}}.\n\nAcesse: {{app_url}}/login\n\nEquipe {{app_name}}",
            'user_registration_pending' => "Cadastro em Análise\n\nOlá {{user_name}},\n\nRecebemos seu cadastro no {{app_name}} e ele está sendo analisado pela nossa equipe.\n\nEste processo geralmente leva até 24 horas úteis.\n\nEquipe {{app_name}}",
            'user_approved' => "Conta Aprovada!\n\nOlá {{user_name}},\n\nÓtimas notícias! Sua conta no {{app_name}} foi aprovada em {{approval_date}}.\n\nAcesse: {{login_url}}\n\nEquipe {{app_name}}",
            'user_incomplete_registration' => "Complete seu Cadastro\n\nOlá {{user_name}},\n\nNotamos que você iniciou seu cadastro no {{app_name}}, mas não o finalizou.\n\nComplete: {{complete_registration_url}}\n\nEquipe {{app_name}}",
            'payment_received' => "Pagamento Recebido!\n\nOlá {{user_name}},\n\nVocê recebeu um novo pagamento!\n\nValor: {{amount}}\nMétodo: {{payment_method}}\nID: {{transaction_id}}\nData: {{transaction_date}}\n\nAcesse: {{app_url}}/dashboard\n\nEquipe {{app_name}}",
            'payment_sent' => "Pagamento Enviado\n\nOlá {{user_name}},\n\nSeu pagamento foi processado!\n\nValor: {{amount}}\nID: {{transaction_id}}\n\nAcesse: {{app_url}}/dashboard\n\nEquipe {{app_name}}",
            'payment_pending' => "Pagamento Pendente\n\nOlá {{user_name}},\n\nVocê tem um pagamento aguardando confirmação.\n\nValor: {{amount}}\nID: {{transaction_id}}\n\nFinalize: {{payment_url}}\n\nEquipe {{app_name}}",
            'payment_failed' => "Falha no Pagamento\n\nOlá {{user_name}},\n\nOcorreu uma falha no processamento do seu pagamento.\n\nValor: {{amount}}\nMotivo: {{error_message}}\n\nTente novamente: {{app_url}}/dashboard\n\nEquipe {{app_name}}",
            'checkout_sale' => "Nova Venda Realizada!\n\nOlá {{user_name}},\n\nParabéns! Você realizou uma nova venda!\n\nProduto: {{product_name}}\nValor: {{amount}}\nCliente: {{customer_email}}\n\nAcesse: {{app_url}}/checkout-panel/sales\n\nEquipe {{app_name}}",
            'abandoned_cart' => "Você esqueceu algo?\n\nOlá {{user_name}},\n\nNotamos que você estava interessado no produto {{product_name}} ({{amount}}), mas não finalizou a compra.\n\nFinalize: {{checkout_url}}\n\nEquipe {{app_name}}",
        ];

        return $texts[$type] ?? '';
    }
}


---
trigger: always_on
glob: **/*.{php,js,blade.php}
description: AG PAY System Documentation - Rules and Overview
---

# AG PAY - Sistema de Gateway e Checkout Whitelabel

Este documento contém as diretrizes técnicas, arquitetura e funcionalidades do sistema AG PAY.

## 🛠 Tecnologias Principais
- **Backend:** Laravel 12.x / PHP 8.2+
- **Frontend:** Tailwind CSS (via CDN com configuração dinâmica), Alpine.js 3.x
- **Banco de Dados:** MySQL
- **Servidor:** Linux, gerenciado via aaPanel
- **Infraestrutura:** Cloudflare (SSL/Proxy), integrando Trusted Proxies no Laravel.

## 👥 Perfis e Funcionalidades

### 🔐 Super Admin (Administrador)
Localizado em `/admin`.
- **Dashboard:** Visão geral de transações, saques e volume processado.
- **Gestão de Usuários:** Edição de perfis, controle de saldo (adicionar, remover, congelar), aprovação de contas e KYC.
- **Financeiro:** Monitoramento de transações, processamento e pagamento de saques, gestão de Chargebacks e pedidos MED.
- **Configurações:** 
    - **Personalização:** Cores do sistema (Primária, Secundária, etc.) e logotipos geridos via `ThemeHelper` e `LogoHelper`.
    - **Gateway:** Configuração de adquirentes e taxas de Cash-In (PIX/Cartão) e Cash-Out.
    - **SMTP:** Configuração de envios de e-mail e campanhas.
    - **Landing Page:** Edição de textos e imagens da página inicial pública.
- **Suporte:** Sistema de tickets para atendimento aos usuários.

### 👤 Usuário (Dashboard do Cliente)
Localizado em `/dashboard`.
- **Checkout:** Criação e gestão de links de pagamento (produtos) com suporte a PIX e Cartão de Crédito.
- **Recebimentos:** Geração rápida de QR Code PIX para recebimento presencial ou manual.
- **Financeiro:** Extrato de transações, solicitação de saques, depósitos via PIX e antecipação de recebíveis.
- **Integrações:** Configuração de webhooks e plugins para Shopify e WooCommerce.
- **Segurança:** Autenticação em duas etapas (2FA) e gestão de tokens de API com restrição por IP.
- **Split de Pagamentos:** Configuração de divisão de valores entre contas.
- **Afiliados:** Sistema de comissionamento por indicações.
- **KYC:** Processo obrigatório de validação de identidade (Wizard progressivo com biometria facial).

## 🔀 Fluxos Principais

### 1. Fluxo de Checkout Público
- O cliente acessa `pay/{uuid}`.
- **Identificação:** Coleta de Nome, Email, CPF e Telefone.
- **Pagamento:** Escolha entre PIX ou Cartão de Crédito.
- **Processamento:** 
    - Se PIX: Gera QR Code e chave "Copia e Cola". Monitoramento em tempo real via polling.
    - Se Cartão: Processado via gateway configurado (ex: MercadoPago).
- **Finalização:** Redirecionamento para página de agradecimento ou entrega do produto digital.

### 2. Fluxo de Saque
- Usuário solicita o saque no dashboard -> Valor é debitado do saldo -> Admin recebe notificação -> Admin aprova/paga via chave PIX -> Status atualizado.

## 🛡 Segurança e Padrões de Código

- **Forçar HTTPS:** Em ambiente de produção, o sistema força o esquema HTTPS através do `AppServiceProvider`.
- **Trusted Proxies:** O sistema confia em proxies (Cloudflare) via `bootstrap/app.php` para garantir a geração correta de URLs e detecção de IPs.
- **Tema Dinâmico:** Use sempre as classes do Tailwind configuradas no `ThemeHelper` (ex: `text-theme-primary`, `bg-theme-primary`) em vez de cores fixas no Admin e Checkout.
- **Validação de Cores:** Ao salvar cores hexadecimais, use o formato de array na validação do controller para evitar erros de delimitador de regex: `['regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/']`.
- **KYC:** Proteção `approved` middleware impede acesso a funcionalidades financeiras até que o usuário complete o KYC e seja aprovado.

## 📁 Estrutura de Pastas Relevante
- `app/Helpers`: Contém `ThemeHelper`, `LogoHelper`, `WebhookUrlHelper`.
- `app/Http/Controllers/Admin`: Controladores específicos da área administrativa.
- `resources/views/layouts`: Layouts `admin.blade.php` e `app.blade.php` (Dashboard).
- `resources/views/checkout`: Template da página de pagamento.

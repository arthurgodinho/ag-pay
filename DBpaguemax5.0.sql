-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 03-Mar-2026 às 17:25
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `paguemax`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `abandoned_carts`
--

CREATE TABLE `abandoned_carts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `cart_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`cart_data`)),
  `total_amount` decimal(15,2) NOT NULL,
  `reminder_sent_count` int(11) NOT NULL DEFAULT 0,
  `last_reminder_at` timestamp NULL DEFAULT NULL,
  `recovered_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `api_tokens`
--

CREATE TABLE `api_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` varchar(100) DEFAULT NULL,
  `project` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(128) NOT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `withdrawal_mode` enum('automatic','manual') NOT NULL DEFAULT 'manual',
  `webhook_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `api_token_allowed_ips`
--

CREATE TABLE `api_token_allowed_ips` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `api_token_id` bigint(20) UNSIGNED NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `app_connections`
--

CREATE TABLE `app_connections` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `app_id` varchar(255) NOT NULL,
  `credentials` text DEFAULT NULL,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `connected_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `awards`
--

CREATE TABLE `awards` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `goal_amount` decimal(15,2) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `awards`
--

INSERT INTO `awards` (`id`, `title`, `description`, `goal_amount`, `image_path`, `created_at`, `updated_at`) VALUES
(1, 'Iphone 17 ProMax', 'Prêmio Exclusivo ao atingir R$ 50.000,00 em vendas acumuladas!', 50000.00, 'awards/TGyznJJcJFjZqHagS1D0s5qCyZSuC17TC3ZhloYH.png', '2026-01-06 19:11:42', '2026-03-02 20:22:58'),
(2, 'Placa de 100K', 'Prêmio Exclusivo ao atingir R$ 100.000,00 em vendas acumuladas!', 100000.00, 'awards/o8ZyxYbN4oUL2Ijq9I34oyp0neXMchBHai7b80R6.png', '2026-01-06 19:12:01', '2026-01-06 19:12:01'),
(3, 'Placa de 500K', 'Prêmio Exclusivo ao atingir R$ 500.000,00 em vendas acumuladas!', 500000.00, 'awards/DdD4J3IA75mZznCbk78YVqL3eOtWQME8BuZjTSTR.png', '2026-01-06 19:16:01', '2026-01-06 19:16:01'),
(4, 'Placa de 1M', 'Prêmio Exclusivo ao atingir R$ 1.000.000,00 em vendas acumuladas!', 1000000.00, 'awards/zD8cU4lyozdvUGcWYZAfhlQQjUlb8seqJhWgoTeF.png', '2026-01-06 19:16:23', '2026-01-06 19:16:23'),
(5, 'Placa de 5M', 'Prêmio Exclusivo ao atingir R$ 5.000.000,00 em vendas acumuladas!', 5000000.00, 'awards/ACa2rYA0xK2uTx1HagamyVvge8MV5QmSS8UeRrZS.png', '2026-01-06 19:16:54', '2026-01-06 19:16:54');

-- --------------------------------------------------------

--
-- Estrutura da tabela `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('admin.dashboard.stats_v2', 'a:12:{s:11:\"totalProfit\";s:4:\"2.00\";s:16:\"totalUserBalance\";s:6:\"221.80\";s:10:\"totalUsers\";i:1;s:10:\"pendingKyc\";i:0;s:18:\"pendingWithdrawals\";i:0;s:16:\"totalChargebacks\";i:0;s:17:\"transactionsToday\";i:1;s:21:\"transactionsCompleted\";i:1;s:19:\"transactionsPending\";i:9;s:17:\"totalTransactions\";i:10;s:14:\"saldoCongelado\";s:4:\"0.00\";s:9:\"chartData\";a:3:{s:5:\"dates\";a:7:{i:0;s:5:\"25/02\";i:1;s:5:\"26/02\";i:2;s:5:\"27/02\";i:3;s:5:\"28/02\";i:4;s:5:\"01/03\";i:5;s:5:\"02/03\";i:6;s:5:\"03/03\";}s:7:\"volumes\";a:7:{i:0;d:0;i:1;d:0;i:2;d:0;i:3;d:0;i:4;d:0;i:5;d:0;i:6;d:0;}s:7:\"profits\";a:7:{i:0;d:0;i:1;d:0;i:2;d:0;i:3;d:0;i:4;d:0;i:5;d:0;i:6;d:0;}}}', 1772555502),
('app.default_locale', 's:2:\"pt\";', 1772490153),
('dashboard.stats_v3.2.hoje.todos', 'a:15:{s:13:\"receivedToday\";i:0;s:12:\"totalBilling\";s:6:\"185.80\";s:13:\"salesRealized\";i:0;s:13:\"salesQuantity\";i:1;s:13:\"averageTicket\";d:185.8;s:16:\"balanceToRelease\";i:0;s:12:\"dailyAverage\";d:185.8;s:8:\"pixStats\";a:5:{s:15:\"conversion_rate\";d:11.11111111111111;s:13:\"approval_rate\";d:11.11111111111111;s:5:\"value\";s:6:\"185.80\";s:5:\"total\";i:9;s:9:\"completed\";i:1;}s:11:\"creditStats\";a:5:{s:15:\"conversion_rate\";i:0;s:13:\"approval_rate\";i:0;s:5:\"value\";i:0;s:5:\"total\";i:0;s:9:\"completed\";i:0;}s:11:\"boletoStats\";a:5:{s:15:\"conversion_rate\";i:0;s:13:\"approval_rate\";i:0;s:5:\"value\";i:0;s:5:\"total\";i:0;s:9:\"completed\";i:0;}s:17:\"generalConversion\";d:11.11111111111111;s:14:\"chargebackRate\";i:0;s:16:\"growthPercentage\";d:-100;s:13:\"activeClients\";i:0;s:9:\"chartData\";a:3:{s:5:\"dates\";a:10:{i:0;s:6:\"22 Feb\";i:1;s:6:\"23 Feb\";i:2;s:6:\"24 Feb\";i:3;s:6:\"25 Feb\";i:4;s:6:\"26 Feb\";i:5;s:6:\"27 Feb\";i:6;s:6:\"28 Feb\";i:7;s:6:\"01 Mar\";i:8;s:6:\"02 Mar\";i:9;s:6:\"03 Mar\";}s:7:\"entries\";a:10:{i:0;d:0;i:1;d:0;i:2;d:0;i:3;d:0;i:4;d:0;i:5;d:0;i:6;d:0;i:7;d:0;i:8;d:0;i:9;d:0;}s:5:\"exits\";a:10:{i:0;d:0;i:1;d:0;i:2;d:0;i:3;d:0;i:4;d:0;i:5;d:0;i:6;d:0;i:7;d:0;i:8;d:0;i:9;d:0;}}}', 1772554118),
('setting.advance_fee_percentage', 's:2:\"28\";', 1772554261),
('setting.affiliate_commission_fixed', 's:4:\"0.00\";', 1772553617),
('setting.affiliate_commission_percentage', 's:4:\"0.50\";', 1772553617),
('setting.affiliate_commission_type', 's:10:\"percentage\";', 1772553617),
('setting.cashin_card_fixo', 's:4:\"1.00\";', 1772554226),
('setting.cashin_card_minima', 's:4:\"5.00\";', 1772553617),
('setting.cashin_card_percentual', 's:4:\"6.00\";', 1772554226),
('setting.cashin_fixo', 's:1:\"1\";', 1772554261),
('setting.cashin_pix_fixo', 's:1:\"1\";', 1772554226),
('setting.cashin_pix_minima', 's:1:\"1\";', 1772554261),
('setting.cashin_pix_percentual', 's:4:\"2.00\";', 1772554226),
('setting.cashout_api_percentual', 's:4:\"2.00\";', 1772553617),
('setting.cashout_crypto_percentual', 's:4:\"7.00\";', 1772553617),
('setting.cashout_fixo', 's:1:\"1\";', 1772554261),
('setting.cashout_pix_fixo', 's:1:\"1\";', 1772554226),
('setting.cashout_pix_minima', 's:1:\"1\";', 1772554226),
('setting.cashout_pix_percentual', 's:4:\"2.00\";', 1772554226),
('setting.checkout_boleto_fixo', 's:1:\"1\";', 1772553617),
('setting.checkout_boleto_percentual', 's:4:\"2.50\";', 1772553617),
('setting.checkout_card_fixo', 's:4:\"1.00\";', 1772553617),
('setting.checkout_card_percentual', 's:4:\"6.00\";', 1772553617),
('setting.checkout_pix_fixo', 's:1:\"1\";', 1772553617),
('setting.checkout_pix_percentual', 's:4:\"3.00\";', 1772553617),
('setting.credit_card_transaction_fee_fixed', 's:1:\"1\";', 1772555396),
('setting.credit_card_transaction_fee_percent', 's:1:\"6\";', 1772555396),
('setting.default_gateway_for_all_users', 's:0:\"\";', 1772555396),
('setting.default_gateway_for_card', 's:0:\"\";', 1772555340),
('setting.default_gateway_for_cashin_pix', 's:0:\"\";', 1772555396),
('setting.default_gateway_for_checkout_card', 's:0:\"\";', 1772555396),
('setting.default_gateway_for_checkout_pix', 's:7:\"pluggou\";', 1772555396),
('setting.default_gateway_for_pix', 's:7:\"pluggou\";', 1772555396),
('setting.default_gateway_for_withdrawals', 's:7:\"pluggou\";', 1772555396),
('setting.default_language', 's:2:\"pt\";', 1772553617),
('setting.default_manager_email', 's:20:\"suporte@paguemax.com\";', 1772555082),
('setting.default_manager_name', 's:16:\"Suporte PagueMax\";', 1772555082),
('setting.default_manager_photo', 's:53:\"settings/KGRIZGWszg44pRIJNUWZOfOZyEuMTB2snUyEcR8z.png\";', 1772555082),
('setting.default_whatsapp', 's:11:\"21959396216\";', 1772555082),
('setting.deposit_min_value', 's:1:\"3\";', 1772554261),
('setting.gateway_name', 's:8:\"PagueMax\";', 1772555339),
('setting.kyc_facial_biometrics_enabled', 's:1:\"0\";', 1772553617),
('setting.limit_pf_daily', 's:5:\"20000\";', 1772553616),
('setting.limit_pf_per_cpf', 'N;', 1772486880),
('setting.limit_pf_withdrawal', 's:4:\"5000\";', 1772553616),
('setting.limit_pf_withdrawals_per_cpf', 'N;', 1772486880),
('setting.limit_pj_daily', 's:5:\"50000\";', 1772553616),
('setting.limit_pj_withdrawal', 's:5:\"10000\";', 1772553616),
('setting.theme_accent_color', 's:7:\"#0097c9\";', 1772555339),
('setting.theme_background_color', 's:7:\"#121b2f\";', 1772555339),
('setting.theme_card_bg', 's:7:\"#1a2332\";', 1772555339),
('setting.theme_dashboard_bg', 's:7:\"#121b2f\";', 1772555339),
('setting.theme_landing_bg', 's:7:\"#121b2f\";', 1772555339),
('setting.theme_primary_color', 's:7:\"#0097c9\";', 1772555339),
('setting.theme_secondary_color', 's:7:\"#64748b\";', 1772555339),
('setting.theme_sidebar_bg', 's:7:\"#121b2f\";', 1772555339),
('setting.theme_text_color', 's:7:\"#e2e8f0\";', 1772555339),
('setting.withdrawal_min_value', 's:1:\"3\";', 1772554261),
('setting.withdrawal_mode', 's:4:\"auto\";', 1772555396),
('setting.withdrawals_per_day_pf', 's:1:\"5\";', 1772554261),
('setting.withdrawals_per_day_pj', 's:2:\"10\";', 1772553616);

-- --------------------------------------------------------

--
-- Estrutura da tabela `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `chargebacks`
--

CREATE TABLE `chargebacks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `transaction_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `status` enum('pending','approved','cancelled') NOT NULL DEFAULT 'pending',
  `external_id` varchar(255) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `admin_note` text DEFAULT NULL,
  `withdrawal_blocked` tinyint(1) NOT NULL DEFAULT 0,
  `balance_debited` tinyint(1) NOT NULL DEFAULT 0,
  `account_negativated` tinyint(1) NOT NULL DEFAULT 0,
  `negative_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `checkout_sales`
--

CREATE TABLE `checkout_sales` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `customer_cpf` varchar(14) DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `amount_gross` decimal(15,2) NOT NULL,
  `amount_fee` decimal(15,2) NOT NULL DEFAULT 0.00,
  `amount_net` decimal(15,2) NOT NULL,
  `payment_method` enum('pix','credit_card') NOT NULL,
  `status` enum('pending','paid','refunded','chargeback') NOT NULL DEFAULT 'pending',
  `has_order_bump` tinyint(1) NOT NULL DEFAULT 0,
  `order_bump_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`order_bump_ids`)),
  `gateway_id` varchar(255) DEFAULT NULL,
  `external_ref` varchar(255) DEFAULT NULL,
  `utm_source` varchar(255) DEFAULT NULL,
  `utm_campaign` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `checkout_settings`
--

CREATE TABLE `checkout_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `logo_url` varchar(255) DEFAULT NULL,
  `primary_color` varchar(255) NOT NULL DEFAULT '#10b981',
  `secondary_color` varchar(255) NOT NULL DEFAULT '#059669',
  `custom_css` text DEFAULT NULL,
  `title` varchar(255) NOT NULL DEFAULT 'Finalizar Pagamento',
  `description` text DEFAULT NULL,
  `has_timer` tinyint(1) NOT NULL DEFAULT 0,
  `timer_minutes` int(11) NOT NULL DEFAULT 15,
  `show_product_image` tinyint(1) NOT NULL DEFAULT 1,
  `show_product_description` tinyint(1) NOT NULL DEFAULT 1,
  `enable_pix` tinyint(1) NOT NULL DEFAULT 1,
  `enable_credit_card` tinyint(1) NOT NULL DEFAULT 1,
  `enable_boleto` tinyint(1) NOT NULL DEFAULT 0,
  `terms_text` text DEFAULT NULL,
  `privacy_text` text DEFAULT NULL,
  `success_redirect_url` varchar(255) DEFAULT NULL,
  `cancel_redirect_url` varchar(255) DEFAULT NULL,
  `security_seal_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `checkout_settings`
--

INSERT INTO `checkout_settings` (`id`, `user_id`, `logo_url`, `primary_color`, `secondary_color`, `custom_css`, `title`, `description`, `has_timer`, `timer_minutes`, `show_product_image`, `show_product_description`, `enable_pix`, `enable_credit_card`, `enable_boleto`, `terms_text`, `privacy_text`, `success_redirect_url`, `cancel_redirect_url`, `security_seal_url`, `created_at`, `updated_at`) VALUES
(1, 2, 'IMG/checkout/logos/checkout_logo_1764782940_oOhqRc8grM.png', '#1c10bc', '#051d94', NULL, 'Pagamento 100% Seguro e Automático.', 'No Momento não estamos aceitando Pagamentos via Cartão de Crédito. Somente Pix!', 0, 5, 1, 1, 1, 0, 0, 'Pagamento 100% Seguro e Automático.', 'Por se Tratar de um Produto Digital, Não Efetuamos Reembolso!', 'https://drive.google.com/drive/folders/12kBBGhCwKzZ4d9ABHxnOOoc0h5JTVRDk?usp=sharing', 'https://wa.me/5521959396216', 'https://dph-site-data.s3.eu-west-1.amazonaws.com/storage/searchByImage/image_8d0bc02ee495b84fddca2d2b277b2899.png', '2025-11-24 05:03:07', '2026-01-05 21:08:43');

-- --------------------------------------------------------

--
-- Estrutura da tabela `checkout_user_settings`
--

CREATE TABLE `checkout_user_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `notification_settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`notification_settings`)),
  `integration_settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`integration_settings`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `checkout_user_settings`
--

INSERT INTO `checkout_user_settings` (`id`, `user_id`, `notification_settings`, `integration_settings`, `created_at`, `updated_at`) VALUES
(1, 2, '{\"email_sale_approved\":true,\"email_sale_failed\":true,\"email_daily_report\":false,\"notification_email\":\"djrdmonster@gmail.com\",\"realtime_panel\":true,\"realtime_sound\":true,\"webhooks\":[]}', '{\"google_analytics\":{\"enabled\":false,\"measurement_id\":\"\"},\"facebook_pixel\":{\"enabled\":false,\"pixel_id\":\"\"},\"zapier\":{\"enabled\":false,\"webhook_url\":\"\"}}', '2025-11-26 01:06:59', '2025-11-26 01:07:41');

-- --------------------------------------------------------

--
-- Estrutura da tabela `checkout_webhooks`
--

CREATE TABLE `checkout_webhooks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `secret` varchar(255) DEFAULT NULL,
  `events` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`events`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `retry_attempts` int(11) NOT NULL DEFAULT 3,
  `timeout_seconds` int(11) NOT NULL DEFAULT 30,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `checkout_webhook_logs`
--

CREATE TABLE `checkout_webhook_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `webhook_id` bigint(20) UNSIGNED NOT NULL,
  `event` varchar(255) NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`payload`)),
  `response_status` int(11) DEFAULT NULL,
  `response_body` text DEFAULT NULL,
  `status` enum('pending','success','failed','retrying') NOT NULL DEFAULT 'pending',
  `attempts` int(11) NOT NULL DEFAULT 0,
  `error_message` text DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `coupons`
--

CREATE TABLE `coupons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `type` enum('percentage','fixed') NOT NULL DEFAULT 'percentage',
  `value` decimal(15,2) NOT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `used_count` int(11) NOT NULL DEFAULT 0,
  `valid_from` date DEFAULT NULL,
  `valid_until` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `email_campaigns`
--

CREATE TABLE `email_campaigns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body_html` text NOT NULL,
  `body_text` text DEFAULT NULL,
  `status` enum('draft','scheduled','sending','sent','cancelled') NOT NULL DEFAULT 'draft',
  `scheduled_at` timestamp NULL DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `total_recipients` int(11) NOT NULL DEFAULT 0,
  `sent_count` int(11) NOT NULL DEFAULT 0,
  `failed_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `email_logs`
--

CREATE TABLE `email_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `campaign_id` bigint(20) UNSIGNED DEFAULT NULL,
  `to_email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body_html` text DEFAULT NULL,
  `status` enum('pending','sent','failed','bounced') NOT NULL DEFAULT 'pending',
  `error_message` text DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `email_templates`
--

CREATE TABLE `email_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body_html` text NOT NULL,
  `body_text` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `email_templates`
--

INSERT INTO `email_templates` (`id`, `type`, `subject`, `body_html`, `body_text`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'user_registered', 'Bem-vindo ao {{app_name}}!', '\r\n            <h1 style=\"color: #212529; font-size: 28px; font-weight: 700; margin: 0 0 20px; text-align: center;\">Bem-vindo ao {{app_name}}! 🎉</h1>\r\n            <p style=\"color: #495057; font-size: 16px; margin: 0 0 20px;\">Olá <strong style=\"color: #212529;\">{{user_name}}</strong>,</p>\r\n            <p style=\"color: #495057; font-size: 16px; margin: 0 0 20px;\">É com grande prazer que te damos as boas-vindas ao <strong>{{app_name}}</strong>!</p>\r\n            <p style=\"color: #495057; font-size: 16px; margin: 0 0 20px;\">Seu cadastro foi realizado com sucesso em <strong>{{register_date}}</strong>.</p>\r\n            <p style=\"color: #495057; font-size: 16px; margin: 0 0 30px;\">Agora você pode começar a utilizar todas as funcionalidades da nossa plataforma de pagamentos.</p>\r\n            <table role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"margin: 30px auto;\">\r\n            <tr>\r\n                <td align=\"center\" style=\"border-radius: 8px; background-color: #00B2FF;\">\r\n                    <a href=\"{{app_url}}/login\" style=\"display: inline-block; padding: 14px 32px; color: #ffffff; text-decoration: none; font-weight: 600; font-size: 16px; border-radius: 8px; background-color: #00B2FF;\">Acessar Minha Conta</a>\r\n                </td>\r\n            </tr>\r\n        </table>\r\n            <p style=\"color: #6c757d; font-size: 14px; margin: 30px 0 0; text-align: center;\">Se tiver alguma dúvida, nossa equipe está pronta para ajudar!</p>\r\n            ', 'Bem-vindo ao {{app_name}}!\n\nOlá {{user_name}},\n\nÉ com grande prazer que te damos as boas-vindas ao {{app_name}}!\n\nSeu cadastro foi realizado com sucesso em {{register_date}}.\n\nAcesse: {{app_url}}/login\n\nEquipe {{app_name}}', 1, '2025-12-03 23:56:23', '2025-12-04 00:22:32'),
(2, 'user_registration_pending', 'Seu cadastro está em análise - {{app_name}}', '\r\n            <h1 style=\"color: #212529; font-size: 28px; font-weight: 700; margin: 0 0 20px; text-align: center;\">Cadastro em Análise ⏳</h1>\r\n            <p style=\"color: #495057; font-size: 16px; margin: 0 0 20px;\">Olá <strong style=\"color: #212529;\">{{user_name}}</strong>,</p>\r\n            <p style=\"color: #495057; font-size: 16px; margin: 0 0 20px;\">Recebemos seu cadastro no <strong>{{app_name}}</strong> e ele está sendo analisado pela nossa equipe de segurança.</p>\r\n            <div style=\"background-color: #f8f9fa; border-left: 4px solid #ff9800; padding: 20px; border-radius: 6px; margin: 20px 0;\">\r\n            \r\n                <p style=\"margin: 0; color: #495057; font-size: 15px;\"><strong style=\"color: #ff9800;\">⏱️ Tempo de análise:</strong> Este processo geralmente leva até 24 horas úteis.</p>\r\n                <p style=\"margin: 10px 0 0; color: #495057; font-size: 15px;\"><strong style=\"color: #ff9800;\">📧 Notificação:</strong> Assim que sua conta for aprovada, você receberá um email de confirmação.</p>\r\n            \r\n        </div>\r\n            <p style=\"color: #495057; font-size: 16px; margin: 20px 0 0;\">Obrigado pela sua paciência e por escolher o {{app_name}}!</p>\r\n            ', 'Cadastro em Análise\n\nOlá {{user_name}},\n\nRecebemos seu cadastro no {{app_name}} e ele está sendo analisado pela nossa equipe.\n\nEste processo geralmente leva até 24 horas úteis.\n\nEquipe {{app_name}}', 1, '2025-12-03 23:56:23', '2025-12-04 00:22:32'),
(3, 'user_approved', 'Sua conta foi aprovada! - {{app_name}}', '\r\n            <h1 style=\"color: #212529; font-size: 28px; font-weight: 700; margin: 0 0 20px; text-align: center;\">Conta Aprovada! 🎉</h1>\r\n            <p style=\"color: #495057; font-size: 16px; margin: 0 0 20px;\">Olá <strong style=\"color: #212529;\">{{user_name}}</strong>,</p>\r\n            <p style=\"color: #495057; font-size: 16px; margin: 0 0 20px;\">Ótimas notícias! Sua conta no <strong>{{app_name}}</strong> foi aprovada em <strong>{{approval_date}}</strong>.</p>\r\n            <div style=\"background-color: #f8f9fa; border-left: 4px solid #28a745; padding: 20px; border-radius: 6px; margin: 20px 0;\">\r\n            \r\n                <p style=\"margin: 0; color: #495057; font-size: 15px;\">✅ <strong style=\"color: #28a745;\">Sua conta está ativa!</strong></p>\r\n                <p style=\"margin: 10px 0 0; color: #495057; font-size: 15px;\">Agora você pode acessar todas as funcionalidades da plataforma e começar a usar nossos serviços de pagamento.</p>\r\n            \r\n        </div>\r\n            <table role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"margin: 30px auto;\">\r\n            <tr>\r\n                <td align=\"center\" style=\"border-radius: 8px; background-color: #28a745;\">\r\n                    <a href=\"{{login_url}}\" style=\"display: inline-block; padding: 14px 32px; color: #ffffff; text-decoration: none; font-weight: 600; font-size: 16px; border-radius: 8px; background-color: #28a745;\">Acessar Minha Conta</a>\r\n                </td>\r\n            </tr>\r\n        </table>\r\n            <p style=\"color: #6c757d; font-size: 14px; margin: 30px 0 0; text-align: center;\">Bem-vindo ao {{app_name}}!</p>\r\n            ', 'Conta Aprovada!\n\nOlá {{user_name}},\n\nÓtimas notícias! Sua conta no {{app_name}} foi aprovada em {{approval_date}}.\n\nAcesse: {{login_url}}\n\nEquipe {{app_name}}', 1, '2025-12-03 23:56:23', '2025-12-04 00:22:32'),
(4, 'user_incomplete_registration', 'Complete seu cadastro - {{app_name}}', '\r\n            <h1 style=\"color: #212529; font-size: 28px; font-weight: 700; margin: 0 0 20px; text-align: center;\">Complete seu Cadastro 📝</h1>\r\n            <p style=\"color: #495057; font-size: 16px; margin: 0 0 20px;\">Olá <strong style=\"color: #212529;\">{{user_name}}</strong>,</p>\r\n            <p style=\"color: #495057; font-size: 16px; margin: 0 0 20px;\">Notamos que você iniciou seu cadastro no <strong>{{app_name}}</strong>, mas não o finalizou.</p>\r\n            <div style=\"background-color: #f8f9fa; border-left: 4px solid #ff9800; padding: 20px; border-radius: 6px; margin: 20px 0;\">\r\n            \r\n                <p style=\"margin: 0; color: #495057; font-size: 15px;\">⏰ <strong style=\"color: #ff9800;\">Complete agora</strong> e comece a usar todas as funcionalidades da plataforma!</p>\r\n                <p style=\"margin: 10px 0 0; color: #495057; font-size: 15px;\">O processo leva apenas alguns minutos e você terá acesso completo ao sistema.</p>\r\n            \r\n        </div>\r\n            <table role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"margin: 30px auto;\">\r\n            <tr>\r\n                <td align=\"center\" style=\"border-radius: 8px; background-color: #ff9800;\">\r\n                    <a href=\"{{complete_registration_url}}\" style=\"display: inline-block; padding: 14px 32px; color: #ffffff; text-decoration: none; font-weight: 600; font-size: 16px; border-radius: 8px; background-color: #ff9800;\">Finalizar Cadastro</a>\r\n                </td>\r\n            </tr>\r\n        </table>\r\n            <p style=\"color: #6c757d; font-size: 14px; margin: 30px 0 0; text-align: center;\">Se tiver dúvidas, nossa equipe está pronta para ajudar!</p>\r\n            ', 'Complete seu Cadastro\n\nOlá {{user_name}},\n\nNotamos que você iniciou seu cadastro no {{app_name}}, mas não o finalizou.\n\nComplete: {{complete_registration_url}}\n\nEquipe {{app_name}}', 1, '2025-12-03 23:56:23', '2025-12-04 00:22:32'),
(5, 'payment_received', 'Você recebeu um pagamento! - {{app_name}}', '\r\n            <h1 style=\"color: #212529; font-size: 28px; font-weight: 700; margin: 0 0 20px; text-align: center;\">Pagamento Recebido! 💰</h1>\r\n            <p style=\"color: #495057; font-size: 16px; margin: 0 0 20px;\">Olá <strong style=\"color: #212529;\">{{user_name}}</strong>,</p>\r\n            <p style=\"color: #495057; font-size: 16px; margin: 0 0 20px;\">Você recebeu um novo pagamento!</p>\r\n            <div style=\"background-color: #f8f9fa; border-left: 4px solid #28a745; padding: 20px; border-radius: 6px; margin: 20px 0;\">\r\n            \r\n                <table role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\r\n                    <tr>\r\n                        <td style=\"padding: 8px 0; color: #495057; font-size: 15px;\"><strong style=\"color: #212529;\">💰 Valor:</strong></td>\r\n                        <td style=\"padding: 8px 0; color: #28a745; font-size: 18px; font-weight: 700; text-align: right;\">{{amount}}</td>\r\n                    </tr>\r\n                    <tr>\r\n                        <td style=\"padding: 8px 0; color: #495057; font-size: 15px;\"><strong style=\"color: #212529;\">💳 Método:</strong></td>\r\n                        <td style=\"padding: 8px 0; color: #495057; font-size: 15px; text-align: right;\">{{payment_method}}</td>\r\n                    </tr>\r\n                    <tr>\r\n                        <td style=\"padding: 8px 0; color: #495057; font-size: 15px;\"><strong style=\"color: #212529;\">🆔 ID:</strong></td>\r\n                        <td style=\"padding: 8px 0; color: #6c757d; font-size: 13px; text-align: right; font-family: monospace;\">{{transaction_id}}</td>\r\n                    </tr>\r\n                    <tr>\r\n                        <td style=\"padding: 8px 0; color: #495057; font-size: 15px;\"><strong style=\"color: #212529;\">📅 Data:</strong></td>\r\n                        <td style=\"padding: 8px 0; color: #495057; font-size: 15px; text-align: right;\">{{transaction_date}}</td>\r\n                    </tr>\r\n                </table>\r\n            \r\n        </div>\r\n            <p style=\"color: #28a745; font-size: 16px; font-weight: 600; margin: 20px 0; text-align: center;\">✅ O valor já está disponível na sua conta!</p>\r\n            <table role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"margin: 30px auto;\">\r\n            <tr>\r\n                <td align=\"center\" style=\"border-radius: 8px; background-color: #28a745;\">\r\n                    <a href=\"{{app_url}}/dashboard\" style=\"display: inline-block; padding: 14px 32px; color: #ffffff; text-decoration: none; font-weight: 600; font-size: 16px; border-radius: 8px; background-color: #28a745;\">Ver Detalhes</a>\r\n                </td>\r\n            </tr>\r\n        </table>\r\n            ', 'Pagamento Recebido!\n\nOlá {{user_name}},\n\nVocê recebeu um novo pagamento!\n\nValor: {{amount}}\nMétodo: {{payment_method}}\nID: {{transaction_id}}\nData: {{transaction_date}}\n\nAcesse: {{app_url}}/dashboard\n\nEquipe {{app_name}}', 1, '2025-12-03 23:56:23', '2025-12-04 00:22:32'),
(6, 'payment_sent', 'Pagamento enviado com sucesso - {{app_name}}', '\r\n            <h1 style=\"color: #212529; font-size: 28px; font-weight: 700; margin: 0 0 20px; text-align: center;\">Pagamento Enviado ✅</h1>\r\n            <p style=\"color: #495057; font-size: 16px; margin: 0 0 20px;\">Olá <strong style=\"color: #212529;\">{{user_name}}</strong>,</p>\r\n            <p style=\"color: #495057; font-size: 16px; margin: 0 0 20px;\">Seu pagamento foi processado e enviado com sucesso!</p>\r\n            <div style=\"background-color: #f8f9fa; border-left: 4px solid #2196f3; padding: 20px; border-radius: 6px; margin: 20px 0;\">\r\n            \r\n                <table role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\r\n                    <tr>\r\n                        <td style=\"padding: 8px 0; color: #495057; font-size: 15px;\"><strong style=\"color: #212529;\">💰 Valor:</strong></td>\r\n                        <td style=\"padding: 8px 0; color: #495057; font-size: 18px; font-weight: 700; text-align: right;\">{{amount}}</td>\r\n                    </tr>\r\n                    <tr>\r\n                        <td style=\"padding: 8px 0; color: #495057; font-size: 15px;\"><strong style=\"color: #212529;\">💳 Método:</strong></td>\r\n                        <td style=\"padding: 8px 0; color: #495057; font-size: 15px; text-align: right;\">{{payment_method}}</td>\r\n                    </tr>\r\n                    <tr>\r\n                        <td style=\"padding: 8px 0; color: #495057; font-size: 15px;\"><strong style=\"color: #212529;\">🆔 ID:</strong></td>\r\n                        <td style=\"padding: 8px 0; color: #6c757d; font-size: 13px; text-align: right; font-family: monospace;\">{{transaction_id}}</td>\r\n                    </tr>\r\n                    <tr>\r\n                        <td style=\"padding: 8px 0; color: #495057; font-size: 15px;\"><strong style=\"color: #212529;\">📅 Data:</strong></td>\r\n                        <td style=\"padding: 8px 0; color: #495057; font-size: 15px; text-align: right;\">{{transaction_date}}</td>\r\n                    </tr>\r\n                </table>\r\n            \r\n        </div>\r\n            <p style=\"color: #495057; font-size: 16px; margin: 20px 0; text-align: center;\">O pagamento foi debitado da sua conta.</p>\r\n            <table role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"margin: 30px auto;\">\r\n            <tr>\r\n                <td align=\"center\" style=\"border-radius: 8px; background-color: #2196f3;\">\r\n                    <a href=\"{{app_url}}/dashboard\" style=\"display: inline-block; padding: 14px 32px; color: #ffffff; text-decoration: none; font-weight: 600; font-size: 16px; border-radius: 8px; background-color: #2196f3;\">Ver Detalhes</a>\r\n                </td>\r\n            </tr>\r\n        </table>\r\n            ', 'Pagamento Enviado\n\nOlá {{user_name}},\n\nSeu pagamento foi processado!\n\nValor: {{amount}}\nID: {{transaction_id}}\n\nAcesse: {{app_url}}/dashboard\n\nEquipe {{app_name}}', 1, '2025-12-03 23:56:23', '2025-12-04 00:22:32'),
(7, 'payment_pending', 'Pagamento pendente - {{app_name}}', '\r\n            <h1 style=\"color: #212529; font-size: 28px; font-weight: 700; margin: 0 0 20px; text-align: center;\">Pagamento Pendente ⏳</h1>\r\n            <p style=\"color: #495057; font-size: 16px; margin: 0 0 20px;\">Olá <strong style=\"color: #212529;\">{{user_name}}</strong>,</p>\r\n            <p style=\"color: #495057; font-size: 16px; margin: 0 0 20px;\">Você tem um pagamento aguardando confirmação.</p>\r\n            <div style=\"background-color: #f8f9fa; border-left: 4px solid #ff9800; padding: 20px; border-radius: 6px; margin: 20px 0;\">\r\n            \r\n                <table role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\r\n                    <tr>\r\n                        <td style=\"padding: 8px 0; color: #495057; font-size: 15px;\"><strong style=\"color: #212529;\">💰 Valor:</strong></td>\r\n                        <td style=\"padding: 8px 0; color: #495057; font-size: 18px; font-weight: 700; text-align: right;\">{{amount}}</td>\r\n                    </tr>\r\n                    <tr>\r\n                        <td style=\"padding: 8px 0; color: #495057; font-size: 15px;\"><strong style=\"color: #212529;\">💳 Método:</strong></td>\r\n                        <td style=\"padding: 8px 0; color: #495057; font-size: 15px; text-align: right;\">{{payment_method}}</td>\r\n                    </tr>\r\n                    <tr>\r\n                        <td style=\"padding: 8px 0; color: #495057; font-size: 15px;\"><strong style=\"color: #212529;\">🆔 ID:</strong></td>\r\n                        <td style=\"padding: 8px 0; color: #6c757d; font-size: 13px; text-align: right; font-family: monospace;\">{{transaction_id}}</td>\r\n                    </tr>\r\n                </table>\r\n            \r\n        </div>\r\n            <p style=\"color: #495057; font-size: 16px; margin: 20px 0; text-align: center;\">Para finalizar o pagamento, clique no botão abaixo:</p>\r\n            <table role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"margin: 30px auto;\">\r\n            <tr>\r\n                <td align=\"center\" style=\"border-radius: 8px; background-color: #ff9800;\">\r\n                    <a href=\"{{payment_url}}\" style=\"display: inline-block; padding: 14px 32px; color: #ffffff; text-decoration: none; font-weight: 600; font-size: 16px; border-radius: 8px; background-color: #ff9800;\">Finalizar Pagamento</a>\r\n                </td>\r\n            </tr>\r\n        </table>\r\n            ', 'Pagamento Pendente\n\nOlá {{user_name}},\n\nVocê tem um pagamento aguardando confirmação.\n\nValor: {{amount}}\nID: {{transaction_id}}\n\nFinalize: {{payment_url}}\n\nEquipe {{app_name}}', 1, '2025-12-03 23:56:23', '2025-12-04 00:22:32'),
(8, 'payment_failed', 'Falha no processamento do pagamento - {{app_name}}', '\r\n            <h1 style=\"color: #212529; font-size: 28px; font-weight: 700; margin: 0 0 20px; text-align: center;\">Falha no Pagamento ⚠️</h1>\r\n            <p style=\"color: #495057; font-size: 16px; margin: 0 0 20px;\">Olá <strong style=\"color: #212529;\">{{user_name}}</strong>,</p>\r\n            <p style=\"color: #495057; font-size: 16px; margin: 0 0 20px;\">Infelizmente, ocorreu uma falha no processamento do seu pagamento.</p>\r\n            <div style=\"background-color: #f8f9fa; border-left: 4px solid #dc3545; padding: 20px; border-radius: 6px; margin: 20px 0;\">\r\n            \r\n                <table role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\r\n                    <tr>\r\n                        <td style=\"padding: 8px 0; color: #495057; font-size: 15px;\"><strong style=\"color: #212529;\">💰 Valor:</strong></td>\r\n                        <td style=\"padding: 8px 0; color: #495057; font-size: 18px; font-weight: 700; text-align: right;\">{{amount}}</td>\r\n                    </tr>\r\n                    <tr>\r\n                        <td style=\"padding: 8px 0; color: #495057; font-size: 15px;\"><strong style=\"color: #212529;\">🆔 ID:</strong></td>\r\n                        <td style=\"padding: 8px 0; color: #6c757d; font-size: 13px; text-align: right; font-family: monospace;\">{{transaction_id}}</td>\r\n                    </tr>\r\n                    <tr>\r\n                        <td colspan=\"2\" style=\"padding: 12px 0 0; border-top: 1px solid #dee2e6;\">\r\n                            <p style=\"margin: 0; color: #dc3545; font-size: 14px;\"><strong>❌ Motivo:</strong> {{error_message}}</p>\r\n                        </td>\r\n                    </tr>\r\n                </table>\r\n            \r\n        </div>\r\n            <p style=\"color: #495057; font-size: 16px; margin: 20px 0; text-align: center;\">Por favor, verifique os dados e tente novamente.</p>\r\n            <table role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"margin: 30px auto;\">\r\n            <tr>\r\n                <td align=\"center\" style=\"border-radius: 8px; background-color: #dc3545;\">\r\n                    <a href=\"{{app_url}}/dashboard\" style=\"display: inline-block; padding: 14px 32px; color: #ffffff; text-decoration: none; font-weight: 600; font-size: 16px; border-radius: 8px; background-color: #dc3545;\">Tentar Novamente</a>\r\n                </td>\r\n            </tr>\r\n        </table>\r\n            <p style=\"color: #6c757d; font-size: 14px; margin: 30px 0 0; text-align: center;\">Se o problema persistir, entre em contato com nosso suporte.</p>\r\n            ', 'Falha no Pagamento\n\nOlá {{user_name}},\n\nOcorreu uma falha no processamento do seu pagamento.\n\nValor: {{amount}}\nMotivo: {{error_message}}\n\nTente novamente: {{app_url}}/dashboard\n\nEquipe {{app_name}}', 1, '2025-12-03 23:56:23', '2025-12-04 00:22:32'),
(9, 'checkout_sale', 'Nova venda realizada! - {{app_name}}', '\r\n            <h1 style=\"color: #212529; font-size: 28px; font-weight: 700; margin: 0 0 20px; text-align: center;\">Nova Venda Realizada! 🎉</h1>\r\n            <p style=\"color: #495057; font-size: 16px; margin: 0 0 20px;\">Olá <strong style=\"color: #212529;\">{{user_name}}</strong>,</p>\r\n            <p style=\"color: #495057; font-size: 16px; margin: 0 0 20px;\">Parabéns! Você realizou uma nova venda no seu checkout!</p>\r\n            <div style=\"background-color: #f8f9fa; border-left: 4px solid #28a745; padding: 20px; border-radius: 6px; margin: 20px 0;\">\r\n            \r\n                <table role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\r\n                    <tr>\r\n                        <td style=\"padding: 8px 0; color: #495057; font-size: 15px;\"><strong style=\"color: #212529;\">📦 Produto:</strong></td>\r\n                        <td style=\"padding: 8px 0; color: #495057; font-size: 15px; text-align: right;\">{{product_name}}</td>\r\n                    </tr>\r\n                    <tr>\r\n                        <td style=\"padding: 8px 0; color: #495057; font-size: 15px;\"><strong style=\"color: #212529;\">💰 Valor:</strong></td>\r\n                        <td style=\"padding: 8px 0; color: #28a745; font-size: 18px; font-weight: 700; text-align: right;\">{{amount}}</td>\r\n                    </tr>\r\n                    <tr>\r\n                        <td style=\"padding: 8px 0; color: #495057; font-size: 15px;\"><strong style=\"color: #212529;\">👤 Cliente:</strong></td>\r\n                        <td style=\"padding: 8px 0; color: #495057; font-size: 15px; text-align: right;\">{{customer_email}}</td>\r\n                    </tr>\r\n                    <tr>\r\n                        <td style=\"padding: 8px 0; color: #495057; font-size: 15px;\"><strong style=\"color: #212529;\">📅 Data:</strong></td>\r\n                        <td style=\"padding: 8px 0; color: #495057; font-size: 15px; text-align: right;\">{{sale_date}}</td>\r\n                    </tr>\r\n                    <tr>\r\n                        <td style=\"padding: 8px 0; color: #495057; font-size: 15px;\"><strong style=\"color: #212529;\">🆔 ID:</strong></td>\r\n                        <td style=\"padding: 8px 0; color: #6c757d; font-size: 13px; text-align: right; font-family: monospace;\">#{{sale_id}}</td>\r\n                    </tr>\r\n                </table>\r\n            \r\n        </div>\r\n            <p style=\"color: #28a745; font-size: 16px; font-weight: 600; margin: 20px 0; text-align: center;\">✅ O valor já está disponível na sua conta!</p>\r\n            <table role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"margin: 30px auto;\">\r\n            <tr>\r\n                <td align=\"center\" style=\"border-radius: 8px; background-color: #28a745;\">\r\n                    <a href=\"{{app_url}}/checkout-panel/sales\" style=\"display: inline-block; padding: 14px 32px; color: #ffffff; text-decoration: none; font-weight: 600; font-size: 16px; border-radius: 8px; background-color: #28a745;\">Ver Vendas</a>\r\n                </td>\r\n            </tr>\r\n        </table>\r\n            ', 'Nova Venda Realizada!\n\nOlá {{user_name}},\n\nParabéns! Você realizou uma nova venda!\n\nProduto: {{product_name}}\nValor: {{amount}}\nCliente: {{customer_email}}\n\nAcesse: {{app_url}}/checkout-panel/sales\n\nEquipe {{app_name}}', 1, '2025-12-03 23:56:23', '2025-12-04 00:22:32'),
(10, 'abandoned_cart', 'Você esqueceu algo no seu carrinho? - {{app_name}}', '\r\n            <h1 style=\"color: #212529; font-size: 28px; font-weight: 700; margin: 0 0 20px; text-align: center;\">Você esqueceu algo? 🛒</h1>\r\n            <p style=\"color: #495057; font-size: 16px; margin: 0 0 20px;\">Olá <strong style=\"color: #212529;\">{{user_name}}</strong>,</p>\r\n            <p style=\"color: #495057; font-size: 16px; margin: 0 0 20px;\">Notamos que você estava interessado no produto <strong>{{product_name}}</strong>, mas não finalizou a compra.</p>\r\n            <div style=\"background-color: #f8f9fa; border-left: 4px solid #ff9800; padding: 20px; border-radius: 6px; margin: 20px 0;\">\r\n            \r\n                <table role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\r\n                    <tr>\r\n                        <td style=\"padding: 8px 0; color: #495057; font-size: 15px;\"><strong style=\"color: #212529;\">📦 Produto:</strong></td>\r\n                        <td style=\"padding: 8px 0; color: #495057; font-size: 15px; text-align: right;\">{{product_name}}</td>\r\n                    </tr>\r\n                    <tr>\r\n                        <td style=\"padding: 8px 0; color: #495057; font-size: 15px;\"><strong style=\"color: #212529;\">💰 Valor:</strong></td>\r\n                        <td style=\"padding: 8px 0; color: #495057; font-size: 18px; font-weight: 700; text-align: right;\">{{amount}}</td>\r\n                    </tr>\r\n                </table>\r\n            \r\n        </div>\r\n            <p style=\"color: #495057; font-size: 16px; margin: 20px 0; text-align: center;\">Complete sua compra agora e aproveite!</p>\r\n            <table role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"margin: 30px auto;\">\r\n            <tr>\r\n                <td align=\"center\" style=\"border-radius: 8px; background-color: #ff9800;\">\r\n                    <a href=\"{{checkout_url}}\" style=\"display: inline-block; padding: 14px 32px; color: #ffffff; text-decoration: none; font-weight: 600; font-size: 16px; border-radius: 8px; background-color: #ff9800;\">Finalizar Compra</a>\r\n                </td>\r\n            </tr>\r\n        </table>\r\n            <p style=\"color: #6c757d; font-size: 14px; margin: 30px 0 0; text-align: center;\">⏰ Não perca essa oportunidade!</p>\r\n            ', 'Você esqueceu algo?\n\nOlá {{user_name}},\n\nNotamos que você estava interessado no produto {{product_name}} ({{amount}}), mas não finalizou a compra.\n\nFinalize: {{checkout_url}}\n\nEquipe {{app_name}}', 1, '2025-12-03 23:56:23', '2025-12-04 00:22:32');

-- --------------------------------------------------------

--
-- Estrutura da tabela `error_logs`
--

CREATE TABLE `error_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `level` varchar(255) NOT NULL DEFAULT 'error',
  `type` varchar(255) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `context` text DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `transaction_id` bigint(20) UNSIGNED DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `line` int(11) DEFAULT NULL,
  `trace` text DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `resolved` tinyint(1) NOT NULL DEFAULT 0,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `resolved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `resolution_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `error_logs`
--

INSERT INTO `error_logs` (`id`, `level`, `type`, `title`, `message`, `context`, `user_id`, `transaction_id`, `file`, `line`, `trace`, `ip_address`, `user_agent`, `resolved`, `resolved_at`, `resolved_by`, `resolution_notes`, `created_at`, `updated_at`) VALUES
(101, 'error', 'withdrawal', 'Erro no Processamento de Saque', 'Nenhum adquirente configurado para PIX. Configure um adquirente padrão no painel administrativo. | Exception: Nenhum adquirente configurado para PIX. Configure um adquirente padrão no painel administrativo.', '{\"withdrawal_id\":32,\"user_id\":2,\"amount\":\"3.00\",\"amount_gross\":\"5.00\",\"pix_key\":\"contaveo3barth1@gmail.com\",\"transaction_id\":null}', 2, NULL, 'C:\\xampp\\htdocs\\app\\Http\\Controllers\\AdminWithdrawalController.php', 66, '#0 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Database\\Concerns\\ManagesTransactions.php(35): App\\Http\\Controllers\\AdminWithdrawalController->App\\Http\\Controllers\\{closure}(Object(Illuminate\\Database\\MySqlConnection))\n#1 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Database\\DatabaseManager.php(489): Illuminate\\Database\\Connection->transaction(Object(Closure))\n#2 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Support\\Facades\\Facade.php(363): Illuminate\\Database\\DatabaseManager->__call(\'transaction\', Array)\n#3 C:\\xampp\\htdocs\\app\\Http\\Controllers\\AdminWithdrawalController.php(61): Illuminate\\Support\\Facades\\Facade::__callStatic(\'transaction\', Array)\n#4 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\ControllerDispatcher.php(46): App\\Http\\Controllers\\AdminWithdrawalController->pay(32)\n#5 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Route.php(265): Illuminate\\Routing\\ControllerDispatcher->dispatch(Object(Illuminate\\Routing\\Route), Object(App\\Http\\Controllers\\AdminWithdrawalController), \'pay\')\n#6 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Route.php(211): Illuminate\\Routing\\Route->runController()\n#7 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Router.php(822): Illuminate\\Routing\\Route->run()\n#8 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(180): Illuminate\\Routing\\Router->Illuminate\\Routing\\{closure}(Object(Illuminate\\Http\\Request))\n#9 C:\\xampp\\htdocs\\app\\Http\\Middleware\\AdminMiddleware.php(26): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#10 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): App\\Http\\Middleware\\AdminMiddleware->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#11 C:\\xampp\\htdocs\\app\\Http\\Middleware\\CheckSessionTimeout.php(51): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#12 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): App\\Http\\Middleware\\CheckSessionTimeout->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#13 C:\\xampp\\htdocs\\app\\Http\\Middleware\\SetLocale.php(92): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#14 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): App\\Http\\Middleware\\SetLocale->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#15 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Middleware\\SubstituteBindings.php(50): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#16 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Routing\\Middleware\\SubstituteBindings->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#17 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Auth\\Middleware\\Authenticate.php(63): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#18 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Auth\\Middleware\\Authenticate->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#19 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Middleware\\VerifyCsrfToken.php(87): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#20 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Foundation\\Http\\Middleware\\VerifyCsrfToken->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#21 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\View\\Middleware\\ShareErrorsFromSession.php(48): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#22 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\View\\Middleware\\ShareErrorsFromSession->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#23 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Session\\Middleware\\StartSession.php(120): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#24 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Session\\Middleware\\StartSession.php(63): Illuminate\\Session\\Middleware\\StartSession->handleStatefulRequest(Object(Illuminate\\Http\\Request), Object(Illuminate\\Session\\Store), Object(Closure))\n#25 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Session\\Middleware\\StartSession->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#26 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Cookie\\Middleware\\AddQueuedCookiesToResponse.php(36): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#27 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Cookie\\Middleware\\AddQueuedCookiesToResponse->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#28 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Cookie\\Middleware\\EncryptCookies.php(74): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#29 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Cookie\\Middleware\\EncryptCookies->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#30 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#31 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Router.php(821): Illuminate\\Pipeline\\Pipeline->then(Object(Closure))\n#32 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Router.php(800): Illuminate\\Routing\\Router->runRouteWithinStack(Object(Illuminate\\Routing\\Route), Object(Illuminate\\Http\\Request))\n#33 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Router.php(764): Illuminate\\Routing\\Router->runRoute(Object(Illuminate\\Http\\Request), Object(Illuminate\\Routing\\Route))\n#34 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Router.php(753): Illuminate\\Routing\\Router->dispatchToRoute(Object(Illuminate\\Http\\Request))\n#35 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Kernel.php(200): Illuminate\\Routing\\Router->dispatch(Object(Illuminate\\Http\\Request))\n#36 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(180): Illuminate\\Foundation\\Http\\Kernel->Illuminate\\Foundation\\Http\\{closure}(Object(Illuminate\\Http\\Request))\n#37 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Middleware\\TransformsRequest.php(21): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#38 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Middleware\\ConvertEmptyStringsToNull.php(31): Illuminate\\Foundation\\Http\\Middleware\\TransformsRequest->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#39 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Foundation\\Http\\Middleware\\ConvertEmptyStringsToNull->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#40 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Middleware\\TransformsRequest.php(21): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#41 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Middleware\\TrimStrings.php(51): Illuminate\\Foundation\\Http\\Middleware\\TransformsRequest->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#42 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Foundation\\Http\\Middleware\\TrimStrings->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#43 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Http\\Middleware\\ValidatePostSize.php(27): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#44 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Http\\Middleware\\ValidatePostSize->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#45 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Middleware\\PreventRequestsDuringMaintenance.php(109): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#46 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Foundation\\Http\\Middleware\\PreventRequestsDuringMaintenance->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#47 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Http\\Middleware\\HandleCors.php(48): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#48 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Http\\Middleware\\HandleCors->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#49 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Http\\Middleware\\TrustProxies.php(58): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#50 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Http\\Middleware\\TrustProxies->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#51 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Middleware\\InvokeDeferredCallbacks.php(22): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#52 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Foundation\\Http\\Middleware\\InvokeDeferredCallbacks->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#53 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Http\\Middleware\\ValidatePathEncoding.php(26): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#54 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Http\\Middleware\\ValidatePathEncoding->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#55 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#56 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Kernel.php(175): Illuminate\\Pipeline\\Pipeline->then(Object(Closure))\n#57 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Kernel.php(144): Illuminate\\Foundation\\Http\\Kernel->sendRequestThroughRouter(Object(Illuminate\\Http\\Request))\n#58 C:\\xampp\\htdocs\\public\\index.php(215): Illuminate\\Foundation\\Http\\Kernel->handle(Object(Illuminate\\Http\\Request))\n#59 {main}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 0, NULL, NULL, NULL, '2026-02-08 18:17:46', '2026-02-08 18:17:46'),
(102, 'error', 'withdrawal', 'Erro no Processamento de Saque', 'ZoomPag: Erro na requisição - 404 - {\"message\":\"Cannot POST /v1/pix/cashout\",\"code\":\"HTTP_ERROR\",\"timestamp\":\"2026-02-08T18:35:01.846Z\",\"path\":\"/v1/pix/cashout\"} | Exception: ZoomPag: Erro na requisição - 404 - {\"message\":\"Cannot POST /v1/pix/cashout\",\"code\":\"HTTP_ERROR\",\"timestamp\":\"2026-02-08T18:35:01.846Z\",\"path\":\"/v1/pix/cashout\"}', '{\"withdrawal_id\":34,\"user_id\":2,\"amount\":\"3.00\",\"amount_gross\":\"5.00\",\"pix_key\":\"contaveo3barth1@gmail.com\",\"transaction_id\":null}', 2, NULL, 'C:\\xampp\\htdocs\\app\\Services\\Gateways\\ZoomPagGateway.php', 51, '#0 C:\\xampp\\htdocs\\app\\Services\\Gateways\\ZoomPagGateway.php(198): App\\Services\\Gateways\\ZoomPagGateway->makeRequest(\'POST\', \'/v1/pix/cashout\', Array)\n#1 C:\\xampp\\htdocs\\app\\Http\\Controllers\\AdminWithdrawalController.php(170): App\\Services\\Gateways\\ZoomPagGateway->createPixPayment(3.0, Array, \'695cfb0a-34b6-4...\', \'Saque #34 - Pag...\')\n#2 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Database\\Concerns\\ManagesTransactions.php(35): App\\Http\\Controllers\\AdminWithdrawalController->App\\Http\\Controllers\\{closure}(Object(Illuminate\\Database\\MySqlConnection))\n#3 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Database\\DatabaseManager.php(489): Illuminate\\Database\\Connection->transaction(Object(Closure))\n#4 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Support\\Facades\\Facade.php(363): Illuminate\\Database\\DatabaseManager->__call(\'transaction\', Array)\n#5 C:\\xampp\\htdocs\\app\\Http\\Controllers\\AdminWithdrawalController.php(61): Illuminate\\Support\\Facades\\Facade::__callStatic(\'transaction\', Array)\n#6 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\ControllerDispatcher.php(46): App\\Http\\Controllers\\AdminWithdrawalController->pay(34)\n#7 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Route.php(265): Illuminate\\Routing\\ControllerDispatcher->dispatch(Object(Illuminate\\Routing\\Route), Object(App\\Http\\Controllers\\AdminWithdrawalController), \'pay\')\n#8 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Route.php(211): Illuminate\\Routing\\Route->runController()\n#9 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Router.php(822): Illuminate\\Routing\\Route->run()\n#10 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(180): Illuminate\\Routing\\Router->Illuminate\\Routing\\{closure}(Object(Illuminate\\Http\\Request))\n#11 C:\\xampp\\htdocs\\app\\Http\\Middleware\\AdminMiddleware.php(26): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#12 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): App\\Http\\Middleware\\AdminMiddleware->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#13 C:\\xampp\\htdocs\\app\\Http\\Middleware\\CheckSessionTimeout.php(51): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#14 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): App\\Http\\Middleware\\CheckSessionTimeout->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#15 C:\\xampp\\htdocs\\app\\Http\\Middleware\\SetLocale.php(92): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#16 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): App\\Http\\Middleware\\SetLocale->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#17 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Middleware\\SubstituteBindings.php(50): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#18 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Routing\\Middleware\\SubstituteBindings->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#19 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Auth\\Middleware\\Authenticate.php(63): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#20 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Auth\\Middleware\\Authenticate->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#21 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Middleware\\VerifyCsrfToken.php(87): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#22 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Foundation\\Http\\Middleware\\VerifyCsrfToken->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#23 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\View\\Middleware\\ShareErrorsFromSession.php(48): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#24 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\View\\Middleware\\ShareErrorsFromSession->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#25 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Session\\Middleware\\StartSession.php(120): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#26 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Session\\Middleware\\StartSession.php(63): Illuminate\\Session\\Middleware\\StartSession->handleStatefulRequest(Object(Illuminate\\Http\\Request), Object(Illuminate\\Session\\Store), Object(Closure))\n#27 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Session\\Middleware\\StartSession->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#28 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Cookie\\Middleware\\AddQueuedCookiesToResponse.php(36): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#29 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Cookie\\Middleware\\AddQueuedCookiesToResponse->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#30 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Cookie\\Middleware\\EncryptCookies.php(74): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#31 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Cookie\\Middleware\\EncryptCookies->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#32 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#33 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Router.php(821): Illuminate\\Pipeline\\Pipeline->then(Object(Closure))\n#34 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Router.php(800): Illuminate\\Routing\\Router->runRouteWithinStack(Object(Illuminate\\Routing\\Route), Object(Illuminate\\Http\\Request))\n#35 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Router.php(764): Illuminate\\Routing\\Router->runRoute(Object(Illuminate\\Http\\Request), Object(Illuminate\\Routing\\Route))\n#36 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Router.php(753): Illuminate\\Routing\\Router->dispatchToRoute(Object(Illuminate\\Http\\Request))\n#37 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Kernel.php(200): Illuminate\\Routing\\Router->dispatch(Object(Illuminate\\Http\\Request))\n#38 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(180): Illuminate\\Foundation\\Http\\Kernel->Illuminate\\Foundation\\Http\\{closure}(Object(Illuminate\\Http\\Request))\n#39 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Middleware\\TransformsRequest.php(21): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#40 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Middleware\\ConvertEmptyStringsToNull.php(31): Illuminate\\Foundation\\Http\\Middleware\\TransformsRequest->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#41 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Foundation\\Http\\Middleware\\ConvertEmptyStringsToNull->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#42 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Middleware\\TransformsRequest.php(21): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#43 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Middleware\\TrimStrings.php(51): Illuminate\\Foundation\\Http\\Middleware\\TransformsRequest->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#44 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Foundation\\Http\\Middleware\\TrimStrings->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#45 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Http\\Middleware\\ValidatePostSize.php(27): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#46 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Http\\Middleware\\ValidatePostSize->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#47 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Middleware\\PreventRequestsDuringMaintenance.php(109): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#48 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Foundation\\Http\\Middleware\\PreventRequestsDuringMaintenance->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#49 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Http\\Middleware\\HandleCors.php(48): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#50 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Http\\Middleware\\HandleCors->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#51 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Http\\Middleware\\TrustProxies.php(58): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#52 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Http\\Middleware\\TrustProxies->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#53 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Middleware\\InvokeDeferredCallbacks.php(22): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#54 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Foundation\\Http\\Middleware\\InvokeDeferredCallbacks->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#55 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Http\\Middleware\\ValidatePathEncoding.php(26): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#56 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Http\\Middleware\\ValidatePathEncoding->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#57 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#58 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Kernel.php(175): Illuminate\\Pipeline\\Pipeline->then(Object(Closure))\n#59 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Kernel.php(144): Illuminate\\Foundation\\Http\\Kernel->sendRequestThroughRouter(Object(Illuminate\\Http\\Request))\n#60 C:\\xampp\\htdocs\\public\\index.php(215): Illuminate\\Foundation\\Http\\Kernel->handle(Object(Illuminate\\Http\\Request))\n#61 {main}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 0, NULL, NULL, NULL, '2026-02-08 18:35:02', '2026-02-08 18:35:02'),
(103, 'error', 'withdrawal', 'Erro no Processamento de Saque', 'ZoomPag: Erro na requisição - 401 -  | Exception: ZoomPag: Erro na requisição - 401 - ', '{\"withdrawal_id\":34,\"user_id\":2,\"amount\":\"3.00\",\"amount_gross\":\"5.00\",\"pix_key\":\"contaveo3barth1@gmail.com\",\"transaction_id\":null}', 2, NULL, 'C:\\xampp\\htdocs\\app\\Services\\Gateways\\ZoomPagGateway.php', 51, '#0 C:\\xampp\\htdocs\\app\\Services\\Gateways\\ZoomPagGateway.php(201): App\\Services\\Gateways\\ZoomPagGateway->makeRequest(\'POST\', \'/api/v1/pix/cas...\', Array)\n#1 C:\\xampp\\htdocs\\app\\Http\\Controllers\\AdminWithdrawalController.php(170): App\\Services\\Gateways\\ZoomPagGateway->createPixPayment(3.0, Array, \'073aaf18-aad7-4...\', \'Saque #34 - Pag...\')\n#2 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Database\\Concerns\\ManagesTransactions.php(35): App\\Http\\Controllers\\AdminWithdrawalController->App\\Http\\Controllers\\{closure}(Object(Illuminate\\Database\\MySqlConnection))\n#3 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Database\\DatabaseManager.php(489): Illuminate\\Database\\Connection->transaction(Object(Closure))\n#4 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Support\\Facades\\Facade.php(363): Illuminate\\Database\\DatabaseManager->__call(\'transaction\', Array)\n#5 C:\\xampp\\htdocs\\app\\Http\\Controllers\\AdminWithdrawalController.php(61): Illuminate\\Support\\Facades\\Facade::__callStatic(\'transaction\', Array)\n#6 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\ControllerDispatcher.php(46): App\\Http\\Controllers\\AdminWithdrawalController->pay(34)\n#7 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Route.php(265): Illuminate\\Routing\\ControllerDispatcher->dispatch(Object(Illuminate\\Routing\\Route), Object(App\\Http\\Controllers\\AdminWithdrawalController), \'pay\')\n#8 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Route.php(211): Illuminate\\Routing\\Route->runController()\n#9 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Router.php(822): Illuminate\\Routing\\Route->run()\n#10 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(180): Illuminate\\Routing\\Router->Illuminate\\Routing\\{closure}(Object(Illuminate\\Http\\Request))\n#11 C:\\xampp\\htdocs\\app\\Http\\Middleware\\AdminMiddleware.php(26): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#12 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): App\\Http\\Middleware\\AdminMiddleware->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#13 C:\\xampp\\htdocs\\app\\Http\\Middleware\\CheckSessionTimeout.php(51): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#14 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): App\\Http\\Middleware\\CheckSessionTimeout->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#15 C:\\xampp\\htdocs\\app\\Http\\Middleware\\SetLocale.php(92): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#16 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): App\\Http\\Middleware\\SetLocale->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#17 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Middleware\\SubstituteBindings.php(50): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#18 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Routing\\Middleware\\SubstituteBindings->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#19 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Auth\\Middleware\\Authenticate.php(63): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#20 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Auth\\Middleware\\Authenticate->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#21 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Middleware\\VerifyCsrfToken.php(87): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#22 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Foundation\\Http\\Middleware\\VerifyCsrfToken->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#23 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\View\\Middleware\\ShareErrorsFromSession.php(48): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#24 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\View\\Middleware\\ShareErrorsFromSession->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#25 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Session\\Middleware\\StartSession.php(120): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#26 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Session\\Middleware\\StartSession.php(63): Illuminate\\Session\\Middleware\\StartSession->handleStatefulRequest(Object(Illuminate\\Http\\Request), Object(Illuminate\\Session\\Store), Object(Closure))\n#27 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Session\\Middleware\\StartSession->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#28 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Cookie\\Middleware\\AddQueuedCookiesToResponse.php(36): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#29 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Cookie\\Middleware\\AddQueuedCookiesToResponse->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#30 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Cookie\\Middleware\\EncryptCookies.php(74): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#31 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Cookie\\Middleware\\EncryptCookies->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#32 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#33 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Router.php(821): Illuminate\\Pipeline\\Pipeline->then(Object(Closure))\n#34 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Router.php(800): Illuminate\\Routing\\Router->runRouteWithinStack(Object(Illuminate\\Routing\\Route), Object(Illuminate\\Http\\Request))\n#35 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Router.php(764): Illuminate\\Routing\\Router->runRoute(Object(Illuminate\\Http\\Request), Object(Illuminate\\Routing\\Route))\n#36 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Router.php(753): Illuminate\\Routing\\Router->dispatchToRoute(Object(Illuminate\\Http\\Request))\n#37 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Kernel.php(200): Illuminate\\Routing\\Router->dispatch(Object(Illuminate\\Http\\Request))\n#38 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(180): Illuminate\\Foundation\\Http\\Kernel->Illuminate\\Foundation\\Http\\{closure}(Object(Illuminate\\Http\\Request))\n#39 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Middleware\\TransformsRequest.php(21): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#40 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Middleware\\ConvertEmptyStringsToNull.php(31): Illuminate\\Foundation\\Http\\Middleware\\TransformsRequest->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#41 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Foundation\\Http\\Middleware\\ConvertEmptyStringsToNull->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#42 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Middleware\\TransformsRequest.php(21): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#43 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Middleware\\TrimStrings.php(51): Illuminate\\Foundation\\Http\\Middleware\\TransformsRequest->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#44 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Foundation\\Http\\Middleware\\TrimStrings->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#45 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Http\\Middleware\\ValidatePostSize.php(27): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#46 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Http\\Middleware\\ValidatePostSize->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#47 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Middleware\\PreventRequestsDuringMaintenance.php(109): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#48 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Foundation\\Http\\Middleware\\PreventRequestsDuringMaintenance->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#49 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Http\\Middleware\\HandleCors.php(48): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#50 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Http\\Middleware\\HandleCors->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#51 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Http\\Middleware\\TrustProxies.php(58): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#52 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Http\\Middleware\\TrustProxies->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#53 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Middleware\\InvokeDeferredCallbacks.php(22): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#54 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Foundation\\Http\\Middleware\\InvokeDeferredCallbacks->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#55 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Http\\Middleware\\ValidatePathEncoding.php(26): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#56 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(219): Illuminate\\Http\\Middleware\\ValidatePathEncoding->handle(Object(Illuminate\\Http\\Request), Object(Closure))\n#57 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))\n#58 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Kernel.php(175): Illuminate\\Pipeline\\Pipeline->then(Object(Closure))\n#59 C:\\xampp\\htdocs\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Kernel.php(144): Illuminate\\Foundation\\Http\\Kernel->sendRequestThroughRouter(Object(Illuminate\\Http\\Request))\n#60 C:\\xampp\\htdocs\\public\\index.php(215): Illuminate\\Foundation\\Http\\Kernel->handle(Object(Illuminate\\Http\\Request))\n#61 {main}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 0, NULL, NULL, NULL, '2026-02-08 18:53:46', '2026-02-08 18:53:46');

-- --------------------------------------------------------

--
-- Estrutura da tabela `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `integrations`
--

CREATE TABLE `integrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `platform` enum('shopify','woocommerce') NOT NULL,
  `store_url` varchar(255) DEFAULT NULL,
  `api_key` varchar(255) DEFAULT NULL,
  `api_secret` varchar(255) DEFAULT NULL,
  `webhook_secret` varchar(255) DEFAULT NULL,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `last_sync_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `landing_page_settings`
--

CREATE TABLE `landing_page_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `landing_page_settings`
--

INSERT INTO `landing_page_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'logo', 'IMG/landing/logo/conf_6949c14b6c4f4_1770130244_69820b44120ae.png', '2025-11-24 07:40:31', '2026-02-03 14:50:44'),
(2, 'favicon', 'IMG/landing/favicon/conf_6949c1673eb35_1767721315_695d4963a97ef.png', '2025-11-24 07:40:31', '2026-01-06 17:41:55'),
(3, 'hero_title', 'Receba com Privacidade', '2025-11-24 07:40:31', '2026-03-02 20:44:41'),
(4, 'hero_subtitle', 'Integre pagamentos PIX e Cartão D+2 de forma simples e rápida com a PagueMax.', '2025-11-24 07:40:31', '2026-03-02 20:46:41'),
(5, 'hero_cta_text', 'Começar Agora', '2025-11-24 07:40:31', '2025-11-24 07:40:31'),
(6, 'features_title', 'Por que escolher nosso gateway?', '2025-11-24 07:40:31', '2025-11-24 07:40:31'),
(7, 'features_subtitle', 'Oferecemos a solução completa para seus pagamentos', '2025-11-24 07:40:31', '2025-11-24 07:40:31'),
(8, 'pricing_title', 'Planos e Preços', '2025-11-24 07:40:31', '2025-11-24 07:40:31'),
(9, 'pricing_subtitle', 'Escolha o plano ideal para seu negócio', '2025-11-24 07:40:31', '2025-11-24 07:40:31'),
(10, 'about_title', 'Sobre Nós', '2025-11-24 07:40:31', '2025-11-24 07:40:31'),
(11, 'about_text', 'Somos uma plataforma de pagamentos completa e confiável', '2025-11-24 07:40:31', '2025-11-24 07:40:31'),
(12, 'footer_text', '© 2026 PagueMax. Todos os direitos reservados.', '2025-11-24 07:40:31', '2026-03-02 20:28:19'),
(13, 'numbers_title', 'Confira alguns de nossos números', '2025-11-24 07:40:31', '2025-11-24 07:40:31'),
(14, 'faq_title', NULL, '2025-11-24 07:40:31', '2026-01-06 20:09:54'),
(15, 'hero_image', 'IMG/landing/hero/1_1772484757_69a5f89561819.png', '2025-11-24 07:40:56', '2026-03-02 20:52:37'),
(16, 'about_image', 'landing/about/itKCU6XOM1gRTG0mB1joKMLudlkC13nFQKVUV6Rf.webp', '2025-11-24 07:48:38', '2025-11-24 07:48:38'),
(17, 'meta_title', 'PagueMax - Provedor de Pagamentos Digitais e API Pix', '2025-11-26 23:56:20', '2026-03-02 20:28:19'),
(18, 'meta_description', 'Plataforma completa de pagamentos para usuários e gateways. Processe pagamentos PIX e Cartão via API com taxas competitivas.', '2025-11-26 23:56:20', '2025-11-27 00:25:19'),
(19, 'hero_badge', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(20, 'solutions_title', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(21, 'solutions_subtitle', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(22, 'solution1_title', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(23, 'solution1_text', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(24, 'solution2_title', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(25, 'solution2_text', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(26, 'pricing_note', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(27, 'feature1_title', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(28, 'feature1_text', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(29, 'feature2_title', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(30, 'feature2_text', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(31, 'feature3_title', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(32, 'feature3_text', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(33, 'feature4_title', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(34, 'feature4_text', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(35, 'feature5_title', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(36, 'feature5_text', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(37, 'feature6_title', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(38, 'feature6_text', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(39, 'whitelabel_title', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(40, 'whitelabel_text', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(41, 'whitelabel_item1_title', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(42, 'whitelabel_item1_text', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(43, 'whitelabel_item2_title', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(44, 'whitelabel_item2_text', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(45, 'whitelabel_item3_title', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(46, 'whitelabel_item3_text', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(47, 'whitelabel_item4_title', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(48, 'whitelabel_item4_text', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(49, 'numbers_subtitle', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(50, 'number1_value', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(51, 'number1_label', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(52, 'number2_value', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(53, 'number2_label', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(54, 'number3_value', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(55, 'number3_label', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(56, 'number4_value', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(57, 'number4_label', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(58, 'faq_subtitle', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(59, 'faq1_question', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(60, 'faq1_answer', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(61, 'faq2_question', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(62, 'faq2_answer', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(63, 'faq3_question', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(64, 'faq3_answer', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(65, 'faq4_question', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(66, 'faq4_answer', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(67, 'faq5_question', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(68, 'faq5_answer', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(69, 'faq6_question', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(70, 'faq6_answer', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(71, 'cta_title', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(72, 'cta_text', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(73, 'whatsapp_number', '+5521959396216', '2025-11-26 23:56:20', '2025-11-26 23:59:20'),
(74, 'landing_effect_mode', 'default', '2025-11-26 23:56:20', '2025-12-03 22:49:43'),
(75, 'hero_stats1_value', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(76, 'hero_stats1_label', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(77, 'hero_stats2_value', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(78, 'hero_stats2_label', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(79, 'hero_stats3_value', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(80, 'hero_stats3_label', NULL, '2025-11-26 23:56:20', '2025-11-26 23:56:20'),
(81, 'integration1_title', NULL, '2026-01-06 20:09:53', '2026-01-06 20:09:53'),
(82, 'integration1_text', NULL, '2026-01-06 20:09:53', '2026-01-06 20:09:53'),
(83, 'integration2_title', NULL, '2026-01-06 20:09:53', '2026-01-06 20:09:53'),
(84, 'integration2_text', NULL, '2026-01-06 20:09:53', '2026-01-06 20:09:53'),
(85, 'integration3_title', NULL, '2026-01-06 20:09:53', '2026-01-06 20:09:53'),
(86, 'integration3_text', NULL, '2026-01-06 20:09:54', '2026-01-06 20:09:54'),
(87, 'api_title', NULL, '2026-01-06 20:10:44', '2026-01-06 20:10:44'),
(88, 'api_subtitle', NULL, '2026-01-06 20:10:44', '2026-01-06 20:10:44'),
(89, 'api_text', NULL, '2026-01-06 20:10:44', '2026-01-06 20:10:44'),
(90, 'steps_title', NULL, '2026-01-06 20:10:44', '2026-01-06 20:10:44'),
(91, 'steps_subtitle', NULL, '2026-01-06 20:10:44', '2026-01-06 20:10:44'),
(92, 'step1_title', NULL, '2026-01-06 20:10:44', '2026-01-06 20:10:44'),
(93, 'step1_text', NULL, '2026-01-06 20:10:44', '2026-01-06 20:10:44'),
(94, 'step2_title', NULL, '2026-01-06 20:10:44', '2026-01-06 20:10:44'),
(95, 'step2_text', NULL, '2026-01-06 20:10:44', '2026-01-06 20:10:44'),
(96, 'step3_title', NULL, '2026-01-06 20:10:44', '2026-01-06 20:10:44'),
(97, 'step3_text', NULL, '2026-01-06 20:10:44', '2026-01-06 20:10:44'),
(98, 'whitelabel_use_hero_image', '0', '2026-03-02 20:51:34', '2026-03-02 21:10:37'),
(99, 'whitelabel_image', 'IMG/landing/whitelabel/whitelabel_image_1772486096.png', '2026-03-02 21:04:46', '2026-03-02 21:14:56'),
(100, 'app_title', 'PagueMax agora na palma da sua mão.', '2026-03-02 21:14:56', '2026-03-02 21:17:19'),
(101, 'app_subtitle', NULL, '2026-03-02 21:14:56', '2026-03-02 21:14:56'),
(102, 'app_playstore_url', 'https://paguemax.com/app/PagueMax_1_1.0.apk', '2026-03-02 21:14:56', '2026-03-03 16:04:41');

-- --------------------------------------------------------

--
-- Estrutura da tabela `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_11_22_203043_add_kyc_and_affiliate_fields_to_users_table', 1),
(5, '2025_11_22_203047_create_wallets_table', 1),
(6, '2025_11_22_203049_create_gateway_credentials_table', 1),
(7, '2025_11_22_203052_create_transactions_table', 1),
(8, '2025_11_22_203054_create_withdrawals_table', 1),
(9, '2025_11_22_214614_add_tax_and_block_fields_to_users_table', 1),
(10, '2025_11_22_214616_create_settings_table', 1),
(11, '2025_11_23_000001_create_system_gateway_configs_table', 1),
(12, '2025_11_23_000002_add_fields_to_withdrawals_table', 1),
(13, '2025_11_23_000003_add_is_admin_to_users_table', 1),
(14, '2025_11_23_234423_create_api_tokens_table', 1),
(15, '2025_11_23_235059_create_landing_page_settings_table', 1),
(16, '2025_11_24_002040_create_error_logs_table', 1),
(17, '2025_11_24_002617_add_management_fields_to_users_table', 1),
(18, '2025_11_24_002920_create_support_tickets_table', 1),
(19, '2025_11_24_002923_create_support_messages_table', 1),
(20, '2025_11_24_002926_create_checkout_settings_table', 1),
(21, '2025_11_24_003922_create_notifications_table', 1),
(22, '2025_11_24_004239_add_cpf_cnpj_to_users_table', 1),
(23, '2025_11_24_010506_add_registration_fields_to_users_table', 1),
(24, '2025_11_24_010509_add_pin_to_users_table', 1),
(25, '2025_11_24_014624_update_kyc_status_enum_to_include_approved', 2),
(26, '2025_11_24_015218_update_pin_column_size_in_users_table', 3),
(27, '2025_11_24_015452_add_missing_fields_to_transactions_table', 4),
(28, '2025_11_24_015603_fix_error_logs_table_data_types', 5),
(29, '2025_11_24_015630_add_fixed_tax_fields_to_users_table', 6),
(30, '2025_11_24_020743_create_products_table', 7),
(31, '2025_11_24_030512_update_gateway_provider_enum_to_include_admin', 8),
(32, '2025_11_24_030534_add_address_fields_to_users_table', 8),
(33, '2025_11_24_030547_add_withdrawal_settings_to_users_and_system', 9),
(34, '2025_11_24_030637_enhance_products_table_for_complete_checkout', 9),
(35, '2025_11_24_032617_add_product_id_to_transactions_table', 10),
(36, '2025_11_24_033307_add_payment_methods_to_products_table', 11),
(37, '2025_11_24_035612_add_additional_fields_to_products_table', 12),
(38, '2025_11_24_035617_create_order_bumps_table', 12),
(39, '2025_11_24_041818_add_image_fields_to_landing_page_settings', 13),
(40, '2025_11_24_043112_create_static_pages_table', 13),
(41, '2025_11_24_051715_update_withdrawals_status_enum_to_include_paid', 14),
(42, '2025_11_24_052546_add_amount_gross_and_fee_to_withdrawals_table', 15),
(43, '2025_11_24_053609_add_client_id_and_project_to_api_tokens_table', 16),
(44, '2025_11_24_053615_create_api_token_allowed_ips_table', 16),
(45, '2025_11_24_070657_add_google2fa_to_users_table', 17),
(46, '2025_11_24_160118_add_detailed_fee_settings_to_settings_table', 18),
(47, '2025_11_24_163922_create_payment_splits_table', 19),
(48, '2025_11_24_171533_add_first_withdrawal_completed_to_users_table', 20),
(49, '2025_11_24_173735_create_chargebacks_table', 21),
(50, '2025_11_24_173800_add_negative_balance_to_wallets_table', 21),
(51, '2025_11_24_173821_add_negative_balance_to_wallets_table', 21),
(52, '2025_11_24_174000_add_document_sent_to_users_table', 22),
(53, '2025_11_24_174415_add_document_sent_to_users_table', 22),
(54, '2025_11_24_174500_add_chargeback_to_transactions_status_enum', 23),
(55, '2025_11_24_174848_add_chargeback_to_transactions_status_enum', 23),
(56, '2025_11_24_175000_add_attachment_to_support_messages_table', 24),
(57, '2025_11_24_175201_add_attachment_to_support_messages_table', 24),
(58, '2025_11_24_181145_add_external_id_to_withdrawals_table', 25),
(59, '2025_11_24_181511_add_default_fields_to_system_gateway_configs_table', 26),
(60, '2025_11_24_182000_add_default_fields_to_system_gateway_configs_table', 26),
(61, '2025_11_24_183840_add_default_gateway_for_all_users_to_settings_table', 27),
(62, '2025_11_24_190530_add_expires_at_to_transactions_table', 28),
(63, '2025_11_24_195538_add_profile_photo_to_users_table', 29),
(64, '2025_11_25_002707_update_users_table_for_kyc_wizard', 30),
(65, '2025_11_25_012101_add_mediation_status_to_transactions_table', 31),
(66, '2025_11_25_012312_add_mediation_to_transactions_status_enum', 31),
(67, '2025_11_25_163324_add_checkout_visual_fields_to_products_table', 32),
(68, '2025_11_25_163327_create_product_order_bumps_table', 32),
(70, '2025_11_25_173032_add_taxa_extorno_to_users_table', 33),
(71, '2025_11_25_193403_create_checkout_sales_table', 34),
(72, '2025_11_25_195403_create_upsells_table', 35),
(73, '2025_11_25_195426_create_coupons_table', 35),
(75, '2025_11_25_200355_create_app_connections_table', 36),
(76, '2025_11_25_205458_add_timer_fields_to_checkout_settings_table', 37),
(77, '2025_11_25_210120_add_security_seal_image_to_checkout_settings_table', 37),
(78, '2025_11_25_220222_create_checkout_user_settings_table', 38),
(79, '2025_11_25_221056_create_checkout_webhooks_table', 39),
(80, '2025_11_25_221059_create_checkout_webhook_logs_table', 39),
(81, '2025_11_25_224111_create_webhooks_table', 40),
(82, '2025_11_25_224115_create_webhook_logs_table', 40),
(83, '2025_11_25_225934_add_release_fields_to_transactions_table', 41),
(84, '2025_11_25_225957_add_withdrawal_settings_to_settings_table', 41),
(85, '2025_11_25_230000_add_withdrawal_mode_to_users_table', 42),
(86, '2025_11_26_002719_add_debit_type_to_transactions_table', 43),
(87, '2025_11_26_003000_add_credit_manual_type_to_transactions_table', 44),
(88, '2025_11_26_153511_add_withdrawal_mode_to_api_tokens_table', 45),
(89, '2025_11_26_155302_create_support_notifications_table', 46),
(90, '2025_11_26_171022_add_marketing_config_to_products_table', 47),
(91, '2025_11_27_032239_add_venit_to_gateway_provider_enum_in_transactions_table', 48),
(92, '2025_11_28_182916_add_language_fields_to_users_and_settings_table', 49),
(93, '2025_11_29_001509_add_advance_fee_percentage_to_settings_table', 50),
(94, '2025_11_29_003802_add_advance_type_to_transactions_table', 51),
(95, '2025_11_29_004329_add_system_to_gateway_provider_enum_in_transactions_table', 52),
(96, '2025_11_29_180000_add_pluggou_to_gateway_provider_enum_in_transactions_table', 53),
(97, '2025_12_01_000000_add_performance_indexes', 54),
(98, '2025_12_03_000001_create_integrations_table', 55),
(99, '2025_12_03_152938_add_enable_boleto_to_checkout_settings_table', 56),
(100, '2025_12_03_154444_add_is_active_for_boleto_to_system_gateway_configs_table', 57),
(101, '2025_12_04_000001_create_smtp_settings_table', 58),
(102, '2025_12_04_000002_create_email_templates_table', 58),
(103, '2025_12_04_000003_create_email_campaigns_table', 58),
(105, '2025_12_04_000004_create_email_logs_table', 59),
(106, '2025_12_04_000005_create_abandoned_carts_table', 60),
(107, '2025_12_04_000006_add_email_fields_to_users_table', 61),
(108, '2026_01_06_141210_create_awards_table', 62),
(109, '2026_01_06_142248_add_accumulated_balance_to_wallets_table', 63),
(110, '2026_01_11_192653_add_webhook_url_to_api_tokens_table', 64),
(111, '2026_01_11_194406_add_split_fields_to_users_table', 65),
(112, '2026_02_03_104500_add_deleted_at_to_products_table', 66),
(113, '2026_02_03_132306_add_download_url_to_products_table', 67),
(114, '2026_02_03_161651_add_pix_key_and_certificate_to_system_gateway_configs_table', 68),
(115, '2026_02_03_202000_modify_gateway_provider_column_in_transactions', 69),
(116, '2026_02_08_000000_add_paid_at_to_transactions_table', 70),
(117, '2026_03_02_183244_add_is_pushed_to_user_notifications_table', 71);

-- --------------------------------------------------------

--
-- Estrutura da tabela `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `banner_url` varchar(255) DEFAULT NULL,
  `type` enum('info','success','warning','error') NOT NULL DEFAULT 'info',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `starts_at` timestamp NULL DEFAULT NULL,
  `ends_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `order_bumps`
--

CREATE TABLE `order_bumps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `value_from` decimal(15,2) DEFAULT NULL,
  `value_for` decimal(15,2) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `payment_splits`
--

CREATE TABLE `payment_splits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `recipient_user_id` bigint(20) UNSIGNED NOT NULL,
  `split_type` enum('percentage','fixed') NOT NULL DEFAULT 'percentage',
  `split_value` decimal(10,2) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `description` varchar(255) DEFAULT NULL,
  `priority` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `download_url` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `product_type` varchar(255) DEFAULT NULL,
  `charge_type` varchar(255) DEFAULT NULL,
  `warranty_period` int(11) DEFAULT NULL,
  `warranty_days` int(11) DEFAULT NULL,
  `support_whatsapp` varchar(255) DEFAULT NULL,
  `support_email` varchar(255) DEFAULT NULL,
  `deliverable_info` text DEFAULT NULL,
  `use_default_thankyou_page` tinyint(1) NOT NULL DEFAULT 1,
  `thankyou_page_url` varchar(255) DEFAULT NULL,
  `pixel_id` varchar(255) DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `tiktok_id` varchar(255) DEFAULT NULL,
  `pixel_facebook` varchar(255) DEFAULT NULL,
  `pixel_google` varchar(255) DEFAULT NULL,
  `pixel_tiktok` varchar(255) DEFAULT NULL,
  `price` decimal(15,2) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `checkout_title` varchar(255) DEFAULT NULL,
  `checkout_description` text DEFAULT NULL,
  `security_info` text DEFAULT NULL,
  `show_security_badges` tinyint(1) NOT NULL DEFAULT 1,
  `guarantee_info` text DEFAULT NULL,
  `show_guarantee` tinyint(1) NOT NULL DEFAULT 1,
  `enable_pix` tinyint(1) NOT NULL DEFAULT 1,
  `enable_credit_card` tinyint(1) NOT NULL DEFAULT 1,
  `enable_boleto` tinyint(1) NOT NULL DEFAULT 0,
  `payment_methods_info` text DEFAULT NULL,
  `banner_image` varchar(255) DEFAULT NULL,
  `checkout_logo` varchar(255) DEFAULT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `show_product_image` tinyint(1) NOT NULL DEFAULT 1,
  `background_color` varchar(7) NOT NULL DEFAULT '#0f172a',
  `primary_color` varchar(7) DEFAULT '#10b981',
  `background_image` varchar(255) DEFAULT NULL,
  `has_timer` tinyint(1) NOT NULL DEFAULT 0,
  `timer_minutes` int(11) NOT NULL DEFAULT 15,
  `has_social_proof` tinyint(1) NOT NULL DEFAULT 0,
  `fake_reviews_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`fake_reviews_json`)),
  `order_bump_active` tinyint(1) NOT NULL DEFAULT 0,
  `order_bump_product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `order_bump_price` decimal(15,2) DEFAULT NULL,
  `order_bump_title` varchar(255) DEFAULT NULL,
  `order_bump_description` text DEFAULT NULL,
  `active_order_bump_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`active_order_bump_ids`)),
  `active_upsell_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`active_upsell_ids`)),
  `views_count` int(11) NOT NULL DEFAULT 0,
  `sales_count` int(11) NOT NULL DEFAULT 0,
  `total_revenue` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `products`
--

INSERT INTO `products` (`id`, `user_id`, `uuid`, `name`, `description`, `download_url`, `category`, `product_type`, `charge_type`, `warranty_period`, `warranty_days`, `support_whatsapp`, `support_email`, `deliverable_info`, `use_default_thankyou_page`, `thankyou_page_url`, `pixel_id`, `google_id`, `tiktok_id`, `pixel_facebook`, `pixel_google`, `pixel_tiktok`, `price`, `is_active`, `checkout_title`, `checkout_description`, `security_info`, `show_security_badges`, `guarantee_info`, `show_guarantee`, `enable_pix`, `enable_credit_card`, `enable_boleto`, `payment_methods_info`, `banner_image`, `checkout_logo`, `product_image`, `show_product_image`, `background_color`, `primary_color`, `background_image`, `has_timer`, `timer_minutes`, `has_social_proof`, `fake_reviews_json`, `order_bump_active`, `order_bump_product_id`, `order_bump_price`, `order_bump_title`, `order_bump_description`, `active_order_bump_ids`, `active_upsell_ids`, `views_count`, `sales_count`, `total_revenue`, `created_at`, `updated_at`, `deleted_at`) VALUES
(15, 2, '179357cb-3637-4aad-863e-8a7e9ea566f5', 'teste novo', 'Produto Maravilho!!!', 'https://drive.google.com/drive/folders/1WEfzsZX5AJPc--ZDmSPnvUPzMLgYEzuk?usp=sharing', NULL, NULL, NULL, NULL, 7, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 185.80, 1, NULL, NULL, NULL, 1, NULL, 1, 1, 1, 0, NULL, NULL, NULL, 'https://hotmart.s3.amazonaws.com/product_pictures/44945a84-dd7b-463b-86d0-f14debd195df/MTODOEURO.jpg', 1, '#0f172a', '#10b981', NULL, 0, 15, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0.00, '2026-02-03 17:12:31', '2026-02-03 17:12:31', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `product_order_bumps`
--

CREATE TABLE `product_order_bumps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `bump_product_id` bigint(20) UNSIGNED NOT NULL,
  `discounted_price` decimal(15,2) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('13eQUhxZN2sYZJEXVKI8V9O6EFlQJNcznsAVBiTB', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiWk9ncmVpMlFyOGpYc1k4dkZkaVBBQzJHSjlNckF2NXpkVVBnWTNXayI7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MjtzOjY6ImxvY2FsZSI7czoyOiJwdCI7czoxMzoibGFzdF9hY3Rpdml0eSI7aToxNzcwNTk2MTQ1O3M6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM2OiJodHRwOi8vbG9jYWxob3N0L3B1YmxpYy9hZG1pbi9hd2FyZHMiO3M6NToicm91dGUiO3M6MTg6ImFkbWluLmF3YXJkcy5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770596145),
('20dVxGHwvrbIAqmK3vGiEwnE4JR3b5uZQ8f0IIiF', NULL, NULL, '', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoid093UGVYd2c2UGRGZHBaRVZCSmZFektubktpdGhjQW5icVdEWjBGdSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6ODoiaHR0cDovLzoiO3M6NToicm91dGUiO3M6MTM6ImxhbmRpbmcuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770595862),
('48pysghrTbsrh0wk8nRFsoE4Wnx1y5XVrOb8e4A1', NULL, NULL, '', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZ1RhMmpVZ2tsSmNTMjhYZnM1ekx6WE96TVVxcUdXVlU0NGRWcEZCVSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6ODoiaHR0cDovLzoiO3M6NToicm91dGUiO3M6MTM6ImxhbmRpbmcuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770594833),
('pzolgPUPm463R1R8kr0yMbDVDVXLTjzKr41XAkOF', NULL, NULL, '', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUzJWbjNWMU00ZDVndXlzM1c3MEoyaDR3d3RXR1FiMm8xMTlwZUFieCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6ODoiaHR0cDovLzoiO3M6NToicm91dGUiO3M6MTM6ImxhbmRpbmcuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770594141),
('R8NF92VQirGbZeZr1lVIrWfVC8eBD3ie3wZXkkmt', NULL, NULL, '', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiU2RxazJRUnJqYklrSHVZRWlXUENFUUdMdFVxU0FxcllvN0RrcWp0OCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6ODoiaHR0cDovLzoiO3M6NToicm91dGUiO3M6MTM6ImxhbmRpbmcuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770595138),
('tOvVoPuT8ioOQsMidxI0kSq9batHIJMN0t1NF91p', NULL, NULL, '', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiR3FocDdsSEtWaHRnZmIyTW5IblpYdEx2U0ZUeDFqU212Z2c4bTB2WCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6ODoiaHR0cDovLzoiO3M6NToicm91dGUiO3M6MTM6ImxhbmRpbmcuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770594182),
('y9PMKAnRRAqgT6V6ppKOCwYT9DwYYEDpTYoAqcEu', NULL, NULL, '', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidG5zb3pjYkFZVjl6clBTd2pvWWtFVGVLODdaWlhRaHp0WHVaR3F1YyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6ODoiaHR0cDovLzoiO3M6NToicm91dGUiO3M6MTM6ImxhbmRpbmcuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770594600);

-- --------------------------------------------------------

--
-- Estrutura da tabela `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `withdrawal_auto_global` tinyint(1) NOT NULL DEFAULT 1,
  `default_gateway_for_all_users` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `created_at`, `updated_at`, `withdrawal_auto_global`, `default_gateway_for_all_users`) VALUES
(1, 'taxa_padrao', '2.99', '2025-11-24 05:59:29', '2025-11-24 05:59:29', 1, NULL),
(2, 'taxa_pix', '1.99', '2025-11-24 05:59:29', '2025-11-24 05:59:29', 1, NULL),
(3, 'taxa_cartao', '5.99', '2025-11-24 05:59:29', '2025-11-24 08:12:39', 1, NULL),
(4, 'cashin_fixo', '1', '2025-11-24 05:59:29', '2025-11-26 01:40:45', 1, NULL),
(5, 'cashin_percentual', '2.00', '2025-11-24 05:59:29', '2026-01-06 18:57:04', 1, NULL),
(6, 'cashout_fixo', '1', '2025-11-24 05:59:29', '2025-11-26 01:40:45', 1, NULL),
(7, 'cashout_percentual', '2.00', '2025-11-24 05:59:29', '2025-11-29 00:44:13', 1, NULL),
(8, 'default_whatsapp', '21959396216', '2025-11-24 05:59:29', '2025-11-24 05:59:29', 1, NULL),
(9, 'default_manager_name', 'Suporte PagueMax', '2025-11-24 05:59:29', '2026-02-04 00:16:24', 1, NULL),
(10, 'default_manager_email', 'suporte@paguemax.com', '2025-11-24 05:59:29', '2026-02-04 00:16:24', 1, NULL),
(11, 'theme_primary_color', '#0097c9', '2025-11-24 05:59:29', '2025-11-24 08:09:29', 1, NULL),
(12, 'theme_secondary_color', '#64748b', '2025-11-24 05:59:29', '2025-11-24 05:59:29', 1, NULL),
(13, 'theme_accent_color', '#0097c9', '2025-11-24 05:59:29', '2025-11-24 08:09:29', 1, NULL),
(14, 'theme_background_color', '#121b2f', '2025-11-24 05:59:29', '2025-11-24 08:09:29', 1, NULL),
(15, 'theme_dashboard_bg', '#121b2f', '2025-11-24 05:59:29', '2025-11-24 08:09:29', 1, NULL),
(16, 'theme_landing_bg', '#121b2f', '2025-11-24 05:59:29', '2025-11-24 08:09:29', 1, NULL),
(17, 'theme_sidebar_bg', '#121b2f', '2025-11-24 05:59:29', '2025-11-24 08:09:29', 1, NULL),
(18, 'theme_text_color', '#e2e8f0', '2025-11-24 05:59:29', '2025-11-24 05:59:29', 1, NULL),
(19, 'theme_card_bg', '#1a2332', '2025-11-24 05:59:29', '2025-11-24 08:09:29', 1, NULL),
(20, 'limit_pf_daily', '20000', '2025-11-24 19:19:36', '2026-01-07 14:57:08', 1, NULL),
(21, 'limit_pf_withdrawal', '5000', '2025-11-24 19:19:36', '2025-11-25 06:42:43', 1, NULL),
(22, 'limit_pf_per_cpf', NULL, '2025-11-24 19:19:36', '2025-11-24 19:19:36', 1, NULL),
(23, 'limit_pf_withdrawals_per_cpf', NULL, '2025-11-24 19:19:36', '2025-11-24 19:19:36', 1, NULL),
(24, 'deposit_percentual', '3.00', '2025-11-24 19:19:36', '2025-11-24 19:53:24', 1, NULL),
(25, 'deposit_fixo', '2.00', '2025-11-24 19:19:36', '2025-11-24 19:53:24', 1, NULL),
(26, 'deposit_min_value', '3', '2025-11-24 19:19:36', '2026-02-08 18:17:28', 1, NULL),
(27, 'cashout_pix_percentual', '2.00', '2025-11-24 19:19:36', '2025-11-29 00:44:13', 1, NULL),
(28, 'cashout_pix_minima', '1', '2025-11-24 19:19:36', '2026-01-07 14:56:31', 1, NULL),
(29, 'cashout_pix_fixo', '1', '2025-11-24 19:19:36', '2025-11-26 01:40:45', 1, NULL),
(30, 'withdrawal_min_value', '3', '2025-11-24 19:19:36', '2026-02-08 18:17:28', 1, NULL),
(31, 'limit_pf_monthly', '50000.00', '2025-11-24 19:19:36', '2025-11-24 19:19:36', 1, NULL),
(32, 'cashout_api_percentual', '2.00', '2025-11-24 19:19:36', '2025-11-26 21:17:37', 1, NULL),
(33, 'cashout_crypto_percentual', '7.00', '2025-11-24 19:19:36', '2025-11-24 19:19:36', 1, NULL),
(34, 'cashin_pix_fixo', '1', '2025-11-24 19:19:36', '2025-11-26 01:40:45', 1, NULL),
(35, 'cashin_pix_percentual', '2.00', '2025-11-24 19:19:36', '2026-01-06 18:57:04', 1, NULL),
(36, 'cashin_card_fixo', '1.00', '2025-11-24 19:19:36', '2025-11-24 19:19:36', 1, NULL),
(37, 'cashin_card_percentual', '6.00', '2025-11-24 19:19:36', '2025-11-24 19:19:36', 1, NULL),
(38, 'default_gateway_for_all_users', '', '2025-11-25 01:39:43', '2025-11-25 07:35:07', 1, NULL),
(39, 'withdrawals_per_day_pf', '5', '2025-11-25 06:42:11', '2026-01-07 14:57:08', 1, NULL),
(40, 'withdrawals_per_day_pj', '10', '2025-11-25 06:42:11', '2026-01-07 14:57:08', 1, NULL),
(41, 'limit_pj_daily', '50000', '2025-11-25 06:42:11', '2025-11-28 22:30:40', 1, NULL),
(42, 'limit_pj_withdrawal', '10000', '2025-11-25 06:42:11', '2025-11-25 06:42:43', 1, NULL),
(43, 'default_gateway_for_pix', 'pluggou', '2025-11-25 07:35:07', '2026-02-08 19:17:22', 1, NULL),
(44, 'default_gateway_for_withdrawals', 'pluggou', '2025-11-25 07:35:07', '2026-02-08 19:17:22', 1, NULL),
(45, 'default_gateway_for_card', '', '2025-11-25 07:35:07', '2025-11-27 05:34:12', 1, NULL),
(46, 'checkout_pix_fixo', '1', '2025-11-26 00:48:06', '2025-11-26 01:40:45', 1, NULL),
(47, 'checkout_pix_percentual', '3.00', '2025-11-26 00:48:06', '2025-11-26 21:17:37', 1, NULL),
(48, 'checkout_card_fixo', '1.00', '2025-11-26 00:48:06', '2025-11-26 00:48:06', 1, NULL),
(49, 'checkout_card_percentual', '6.00', '2025-11-26 00:48:06', '2025-11-26 00:48:06', 1, NULL),
(50, 'card_release_days', '3', '2025-11-26 02:01:04', '2025-11-28 22:38:58', 1, NULL),
(51, 'withdrawal_mode', 'auto', '2025-11-26 02:01:04', '2025-11-30 22:00:47', 1, NULL),
(52, 'default_gateway_for_checkout_pix', 'pluggou', '2025-11-26 02:17:27', '2026-02-08 19:17:22', 1, NULL),
(53, 'default_gateway_for_checkout_card', '', '2025-11-26 02:17:27', '2026-03-03 16:24:55', 1, NULL),
(54, 'cashin_pix_minima', '1', '2025-11-26 21:17:37', '2026-01-11 23:10:38', 1, NULL),
(55, 'cashin_card_minima', '5.00', '2025-11-26 21:17:37', '2026-01-06 18:57:04', 1, NULL),
(56, 'default_gateway_for_cashin_pix', '', '2025-11-26 21:48:19', '2026-03-02 20:21:06', 1, NULL),
(57, 'gateway_name', 'PagueMax', '2025-11-26 23:58:12', '2026-02-04 00:16:14', 1, NULL),
(58, 'default_language', 'pt', '2025-11-28 21:36:53', '2025-11-28 22:30:45', 1, NULL),
(59, 'advance_fee_percentage', '28', '2025-11-29 03:30:59', '2026-01-06 15:14:27', 1, NULL),
(60, 'affiliate_commission_type', 'percentage', '2025-12-03 18:02:12', '2025-12-03 18:02:12', 1, NULL),
(61, 'affiliate_commission_percentage', '0.50', '2025-12-03 18:02:12', '2025-12-03 18:02:12', 1, NULL),
(62, 'affiliate_commission_fixed', '0.00', '2025-12-03 18:02:12', '2025-12-03 18:02:12', 1, NULL),
(63, 'kyc_facial_biometrics_enabled', '0', '2025-12-03 18:02:12', '2025-12-11 16:09:30', 1, NULL),
(64, 'checkout_boleto_fixo', '1', '2025-12-04 00:54:13', '2025-12-04 00:54:13', 1, NULL),
(65, 'checkout_boleto_percentual', '2.50', '2025-12-04 00:54:13', '2025-12-04 00:54:13', 1, NULL),
(66, 'facebook_pixel_id', NULL, '2026-01-07 14:51:38', '2026-01-07 14:51:38', 1, NULL),
(67, 'google_ads_id', NULL, '2026-01-07 14:51:38', '2026-01-07 14:51:38', 1, NULL),
(68, 'credit_card_transaction_fee_percent', '6', '2026-02-03 18:11:06', '2026-02-03 18:11:06', 1, NULL),
(69, 'credit_card_transaction_fee_fixed', '1', '2026-02-03 18:11:06', '2026-02-03 18:11:06', 1, NULL),
(70, 'paguemax_api_url', NULL, '2026-02-03 22:48:03', '2026-02-08 19:21:12', 1, NULL),
(71, 'default_manager_photo', 'settings/KGRIZGWszg44pRIJNUWZOfOZyEuMTB2snUyEcR8z.png', '2026-02-03 23:05:18', '2026-02-03 23:05:18', 1, NULL),
(72, 'paguemax_withdrawal_api_url', NULL, '2026-02-03 23:31:37', '2026-02-08 19:21:12', 1, NULL),
(73, 'zoompag_post_url', 'https://api.zoompag.com', '2026-02-08 17:16:57', '2026-02-08 17:32:14', 1, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `smtp_settings`
--

CREATE TABLE `smtp_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `mailer` varchar(255) NOT NULL DEFAULT 'smtp',
  `host` varchar(255) DEFAULT NULL,
  `port` int(11) NOT NULL DEFAULT 587,
  `username` varchar(255) DEFAULT NULL,
  `password` text DEFAULT NULL,
  `encryption` varchar(255) DEFAULT NULL,
  `from_address` varchar(255) NOT NULL,
  `from_name` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `smtp_settings`
--

INSERT INTO `smtp_settings` (`id`, `mailer`, `host`, `port`, `username`, `password`, `encryption`, `from_address`, `from_name`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'smtp', 'smtp.hostinger.com', 465, 'suporte@paguemax.com', 'eyJpdiI6IkhudFJIczdRQi8rWmJ2VFVVeVZRUHc9PSIsInZhbHVlIjoiVXVKRXY3TWx6eCt5Um9DQkV0bUVGd0FiVkRQaHhBYlhKUU5SYWI3a0pBRT0iLCJtYWMiOiI0ZDFiYmU4Y2I1NGY4YzhiZDE4MjdiYTA0MTBjODRiOTA0N2ZkZDNhNTk5ODhlMDA1ZDNiNzA1YWE0MjYwZGY1IiwidGFnIjoiIn0=', 'ssl', 'suporte@paguemax.com', 'PagueMax - Suporte ao Cliente', 1, '2025-12-04 00:30:48', '2026-02-04 00:16:54');

-- --------------------------------------------------------

--
-- Estrutura da tabela `static_pages`
--

CREATE TABLE `static_pages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `slug` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `static_pages`
--

INSERT INTO `static_pages` (`id`, `slug`, `title`, `content`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'termos-uso', 'Termos de Uso', 'TERMOS E CONDIÇÕES DE USO DA PAGUEMAX\r\n\r\n1. ACEITAÇÃO DOS TERMOS\r\n\r\nAo acessar e utilizar os serviços da plataforma PagueMax, você concorda em cumprir e estar vinculado aos seguintes Termos e Condições de Uso. Se você não concorda com qualquer parte destes termos, não deve utilizar nossos serviços.\r\n\r\n2. DEFINIÇÕES\r\n\r\n2.1. \"Plataforma\" refere-se ao PagueMax, sistema de gateway de pagamentos.\r\n2.2. \"Usuário\" refere-se a qualquer pessoa física ou jurídica que utiliza os serviços da Plataforma.\r\n2.3. \"Serviços\" refere-se a todos os serviços oferecidos pela Plataforma, incluindo processamento de pagamentos, gestão financeira e demais funcionalidades.\r\n\r\n3. CADASTRO E CONTA\r\n\r\n3.1. Para utilizar os serviços, o Usuário deve criar uma conta fornecendo informações verdadeiras, precisas e completas.\r\n3.2. O Usuário é responsável por manter a confidencialidade de suas credenciais de acesso.\r\n3.3. O Usuário deve notificar imediatamente a Plataforma sobre qualquer uso não autorizado de sua conta.\r\n\r\n4. OBRIGAÇÕES DO USUÁRIO\r\n\r\n4.1. Utilizar os serviços apenas para fins legais e de acordo com a legislação vigente.\r\n4.2. Não utilizar os serviços para atividades fraudulentas, ilegais ou que violem direitos de terceiros.\r\n4.3. Fornecer informações precisas e atualizadas.\r\n4.4. Manter a segurança de suas credenciais de acesso.\r\n\r\n5. TAXAS E PAGAMENTOS\r\n\r\n5.1. A Plataforma cobra taxas pelos serviços prestados, conforme divulgado no momento da contratação.\r\n5.2. As taxas podem ser alteradas mediante aviso prévio de 30 dias.\r\n5.3. O Usuário concorda em pagar todas as taxas aplicáveis aos serviços utilizados.\r\n\r\n6. LIMITAÇÃO DE RESPONSABILIDADE\r\n\r\n6.1. A Plataforma não se responsabiliza por perdas ou danos decorrentes do uso ou impossibilidade de uso dos serviços.\r\n6.2. A Plataforma não garante que os serviços estarão sempre disponíveis, ininterruptos ou livres de erros.\r\n\r\n7. PROPRIEDADE INTELECTUAL\r\n\r\n7.1. Todo o conteúdo da Plataforma, incluindo textos, gráficos, logos e software, é propriedade da PagueMax.\r\n7.2. O Usuário não pode reproduzir, distribuir ou criar obras derivadas sem autorização prévia.\r\n\r\n8. PRIVACIDADE\r\n\r\n8.1. O tratamento de dados pessoais está sujeito à nossa Política de Privacidade.\r\n8.2. A Plataforma coleta e processa dados pessoais conforme necessário para prestação dos serviços.\r\n\r\n9. RESCISÃO\r\n\r\n9.1. A Plataforma pode suspender ou encerrar a conta do Usuário em caso de violação destes Termos.\r\n9.2. O Usuário pode encerrar sua conta a qualquer momento, mediante solicitação.\r\n\r\n10. ALTERAÇÕES DOS TERMOS\r\n\r\n10.1. A Plataforma reserva-se o direito de modificar estes Termos a qualquer momento.\r\n10.2. Alterações significativas serão comunicadas aos Usuários com antecedência mínima de 30 dias.\r\n\r\n11. LEI APLICÁVEL\r\n\r\n11.1. Estes Termos são regidos pela legislação brasileira.\r\n11.2. Qualquer disputa será resolvida no foro da comarca de São Paulo, SP.\r\n\r\n12. CONTATO\r\n\r\nPara questões sobre estes Termos, entre em contato através do suporte da Plataforma.\r\n\r\nÚltima atualização: 24/11/2025', 1, '2025-11-24 07:37:56', '2025-11-27 00:46:35'),
(2, 'privacidade', 'Política de Privacidade', 'POLÍTICA DE PRIVACIDADE DA PAGUEMAX\r\n\r\n1. INTRODUÇÃO\r\n\r\nA PagueMax está comprometida com a proteção da privacidade e dos dados pessoais de seus usuários. Esta Política de Privacidade descreve como coletamos, usamos, armazenamos e protegemos suas informações pessoais.\r\n\r\n2. DADOS COLETADOS\r\n\r\n2.1. Dados fornecidos pelo usuário:\r\n- Nome completo\r\n- CPF ou CNPJ\r\n- Data de nascimento\r\n- Endereço de e-mail\r\n- Número de telefone\r\n- Endereço residencial/comercial\r\n- Dados bancários (quando necessário)\r\n- Documentos de identificação (KYC)\r\n\r\n2.2. Dados coletados automaticamente:\r\n- Endereço IP\r\n- Informações do dispositivo\r\n- Dados de navegação\r\n- Cookies e tecnologias similares\r\n\r\n3. FINALIDADE DO USO DOS DADOS\r\n\r\nUtilizamos seus dados pessoais para:\r\n- Prestação dos serviços de gateway de pagamentos\r\n- Verificação de identidade (KYC)\r\n- Processamento de transações\r\n- Comunicação com o usuário\r\n- Cumprimento de obrigações legais\r\n- Prevenção de fraudes e lavagem de dinheiro\r\n- Melhoria dos serviços\r\n\r\n4. COMPARTILHAMENTO DE DADOS\r\n\r\n4.1. Compartilhamos dados apenas quando necessário para:\r\n- Prestação dos serviços (adquirentes, processadores de pagamento)\r\n- Cumprimento de obrigações legais\r\n- Prevenção de fraudes\r\n- Com seu consentimento expresso\r\n\r\n4.2. Não vendemos seus dados pessoais a terceiros.\r\n\r\n5. SEGURANÇA DOS DADOS\r\n\r\n5.1. Implementamos medidas técnicas e organizacionais para proteger seus dados.\r\n5.2. Utilizamos criptografia, controles de acesso e monitoramento de segurança.\r\n5.3. Realizamos backups regulares dos dados.\r\n\r\n6. RETENÇÃO DE DADOS\r\n\r\n6.1. Mantemos seus dados pelo tempo necessário para:\r\n- Prestação dos serviços\r\n- Cumprimento de obrigações legais\r\n- Resolução de disputas\r\n- Prevenção de fraudes\r\n\r\n7. DIREITOS DO USUÁRIO\r\n\r\nVocê tem direito a:\r\n- Acesso aos seus dados pessoais\r\n- Correção de dados inexatos\r\n- Exclusão de dados (quando aplicável)\r\n- Portabilidade dos dados\r\n- Revogação do consentimento\r\n- Oposição ao tratamento\r\n\r\n8. COOKIES\r\n\r\n8.1. Utilizamos cookies para melhorar sua experiência.\r\n8.2. Você pode gerenciar as preferências de cookies nas configurações do navegador.\r\n\r\n9. ALTERAÇÕES NA POLÍTICA\r\n\r\n9.1. Podemos atualizar esta Política periodicamente.\r\n9.2. Alterações significativas serão comunicadas aos usuários.\r\n\r\n10. LGPD\r\n\r\n10.1. Esta Política está em conformidade com a Lei Geral de Proteção de Dados (LGPD - Lei 13.709/2018).\r\n10.2. Temos um Encarregado de Proteção de Dados (DPO) para questões relacionadas à privacidade.\r\n\r\n11. CONTATO\r\n\r\nPara exercer seus direitos ou esclarecer dúvidas sobre privacidade, entre em contato:\r\n- E-mail: privacidade@seudominio.com\r\n- Através do suporte da Plataforma\r\n\r\nÚltima atualização: 24/11/2025', 1, '2025-11-24 07:37:56', '2025-11-27 00:46:06'),
(3, 'pld', 'Prevenção à Lavagem de Dinheiro (PLD)', 'POLÍTICA DE PREVENÇÃO À LAVAGEM DE DINHEIRO (PLD)\r\n\r\n1. COMPROMISSO\r\n\r\nA PagueMax está comprometida com a prevenção à lavagem de dinheiro e ao financiamento do terrorismo, em conformidade com a legislação brasileira, especialmente a Lei nº 9.613/1998 e suas alterações.\r\n\r\n2. OBRIGAÇÕES LEGAIS\r\n\r\n2.1. A PagueMax está sujeita às obrigações previstas na legislação de PLD/CFT.\r\n2.2. Realizamos o monitoramento contínuo de transações suspeitas.\r\n2.3. Mantemos registros de todas as operações conforme exigido por lei.\r\n\r\n3. IDENTIFICAÇÃO E VERIFICAÇÃO (KYC)\r\n\r\n3.1. Todos os usuários devem passar por processo de identificação e verificação (KYC).\r\n3.2. Solicitamos e verificamos documentos de identificação.\r\n3.3. Mantemos cadastro atualizado de todos os usuários.\r\n\r\n4. MONITORAMENTO DE TRANSAÇÕES\r\n\r\n4.1. Monitoramos todas as transações em busca de padrões suspeitos.\r\n4.2. Identificamos operações que possam indicar lavagem de dinheiro.\r\n4.3. Analisamos transações incomuns ou acima de limites estabelecidos.\r\n\r\n5. COMUNICAÇÃO DE OPERAÇÕES SUSPEITAS\r\n\r\n5.1. Comunicamos imediatamente ao COAF (Conselho de Controle de Atividades Financeiras) operações suspeitas.\r\n5.2. Mantemos sigilo sobre as comunicações realizadas.\r\n5.3. Não informamos ao cliente sobre a comunicação de operação suspeita.\r\n\r\n6. LIMITES E CONTROLES\r\n\r\n6.1. Estabelecemos limites de transação conforme perfil do usuário.\r\n6.2. Implementamos controles de risco baseados em análise de perfil.\r\n6.3. Realizamos revisões periódicas de limites e controles.\r\n\r\n7. TREINAMENTO E CAPACITAÇÃO\r\n\r\n7.1. Nossa equipe recebe treinamento regular sobre PLD/CFT.\r\n7.2. Mantemos programa de capacitação contínua.\r\n7.3. Atualizamos procedimentos conforme mudanças na legislação.\r\n\r\n8. REGISTROS E DOCUMENTAÇÃO\r\n\r\n8.1. Mantemos registros detalhados de todas as operações.\r\n8.2. Documentamos análises de risco e decisões tomadas.\r\n8.3. Conservamos documentos pelo prazo legal estabelecido.\r\n\r\n9. COOPERAÇÃO COM AUTORIDADES\r\n\r\n9.1. Cooperamos com autoridades competentes em investigações.\r\n9.2. Fornecemos informações quando legalmente exigido.\r\n9.3. Respeitamos ordens judiciais e determinações regulatórias.\r\n\r\n10. PROIBIÇÕES\r\n\r\n10.1. Não processamos transações de origem ou destino desconhecido.\r\n10.2. Não aceitamos operações que violem sanções internacionais.\r\n10.3. Bloqueamos contas envolvidas em atividades suspeitas.\r\n\r\n11. REVISÃO E ATUALIZAÇÃO\r\n\r\n11.1. Esta política é revisada periodicamente.\r\n11.2. Atualizamos procedimentos conforme mudanças na legislação.\r\n11.3. Comunicamos alterações relevantes aos usuários.\r\n\r\n12. CONTATO\r\n\r\nPara questões relacionadas a PLD/CFT:\r\n- E-mail: compliance@seudominio.com\r\n- Através do suporte da Plataforma\r\n\r\nÚltima atualização: 24/11/2025', 1, '2025-11-24 07:37:56', '2025-11-27 00:46:18'),
(4, 'manual-kyc', 'Manual KYC - Conheça seu Cliente', 'MANUAL KYC - CONHEÇA SEU CLIENTE\r\n\r\n1. O QUE É KYC?\r\n\r\nKYC (Know Your Customer - Conheça seu Cliente) é um processo de identificação e verificação de identidade que garante a segurança e conformidade da plataforma PagueMax.\r\n\r\n2. POR QUE É NECESSÁRIO?\r\n\r\n2.1. Cumprimento legal: Exigido por lei para prevenção à lavagem de dinheiro.\r\n2.2. Segurança: Protege você e outros usuários contra fraudes.\r\n2.3. Conformidade: Garante que a plataforma opere dentro da legalidade.\r\n\r\n3. QUANDO FAZER O KYC?\r\n\r\n3.1. Ao criar sua conta na plataforma.\r\n3.2. Antes de realizar sua primeira transação.\r\n3.3. Quando solicitado pela plataforma para atualização de dados.\r\n\r\n4. DOCUMENTOS NECESSÁRIOS\r\n\r\n4.1. Para Pessoa Física:\r\n- Documento de identidade com foto (RG, CNH ou RNE)\r\n- CPF\r\n- Comprovante de endereço (conta de luz, água, telefone ou extrato bancário)\r\n- Selfie segurando o documento de identidade\r\n\r\n4.2. Para Pessoa Jurídica:\r\n- Contrato Social ou Estatuto Social\r\n- CNPJ\r\n- Documento de identidade do representante legal\r\n- Comprovante de endereço da empresa\r\n- Selfie do representante legal segurando documento\r\n\r\n5. COMO ENVIAR OS DOCUMENTOS\r\n\r\n5.1. Acesse a seção KYC no seu dashboard.\r\n5.2. Preencha seus dados de endereço completo.\r\n3. Faça upload dos documentos solicitados.\r\n4. Envie uma selfie segurando seu documento de identidade.\r\n5. Aguarde a análise da equipe.\r\n\r\n6. REQUISITOS DAS FOTOS\r\n\r\n6.1. Documentos:\r\n- Fotos nítidas e legíveis\r\n- Todas as informações visíveis\r\n- Sem cortes ou partes faltando\r\n- Boa iluminação\r\n\r\n6.2. Selfie:\r\n- Rosto claramente visível\r\n- Documento de identidade visível na mão\r\n- Boa iluminação\r\n- Sem óculos escuros ou objetos cobrindo o rosto\r\n\r\n7. PRAZO DE ANÁLISE\r\n\r\n7.1. Análise geralmente concluída em até 48 horas úteis.\r\n7.2. Em caso de necessidade de informações adicionais, você será notificado.\r\n7.3. O prazo pode variar conforme volume de solicitações.\r\n\r\n8. STATUS DO KYC\r\n\r\n8.1. Pendente: Documentos enviados, aguardando análise.\r\n8.2. Aprovado: KYC concluído com sucesso, conta liberada.\r\n8.3. Rejeitado: Documentos não atendem aos requisitos (você será notificado do motivo).\r\n\r\n9. O QUE FAZER SE FOR REJEITADO?\r\n\r\n9.1. Verifique o motivo da rejeição na notificação.\r\n9.2. Corrija os problemas identificados.\r\n9.3. Reenvie os documentos corrigidos.\r\n9.4. Entre em contato com o suporte se tiver dúvidas.\r\n\r\n10. SEGURANÇA DOS DADOS\r\n\r\n10.1. Seus documentos são armazenados com segurança.\r\n10.2. Utilizamos criptografia para proteção dos dados.\r\n10.3. Apenas equipe autorizada tem acesso aos documentos.\r\n10.4. Dados são mantidos conforme exigências legais.\r\n\r\n11. ATUALIZAÇÃO DE DADOS\r\n\r\n11.1. Mantenha seus dados sempre atualizados.\r\n11.2. Informe alterações de endereço ou documentos.\r\n11.3. A plataforma pode solicitar atualização periódica.\r\n\r\n12. DÚVIDAS FREQUENTES\r\n\r\n12.1. Posso usar a plataforma sem KYC?\r\nNão, o KYC é obrigatório para uso completo da plataforma.\r\n\r\n12.2. Meus dados estão seguros?\r\nSim, seguimos rigorosos padrões de segurança e privacidade.\r\n\r\n12.3. Quanto tempo leva a aprovação?\r\nGeralmente até 48 horas úteis.\r\n\r\n13. CONTATO\r\n\r\nPara dúvidas sobre o processo KYC:\r\n- Através do suporte da plataforma\r\n- E-mail: suporte@seudominio.com\r\n\r\nÚltima atualização: 24/11/2025', 1, '2025-11-24 07:37:56', '2025-11-27 00:45:48');

-- --------------------------------------------------------

--
-- Estrutura da tabela `support_messages`
--

CREATE TABLE `support_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ticket_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `attachment_name` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `support_messages`
--

INSERT INTO `support_messages` (`id`, `ticket_id`, `user_id`, `message`, `attachment`, `attachment_name`, `is_read`, `read_at`, `created_at`, `updated_at`) VALUES
(24, 13, 2, 'dfasa', NULL, NULL, 0, NULL, '2026-02-08 14:43:26', '2026-02-08 14:43:26'),
(25, 14, 2, 'pf me ajuda', NULL, NULL, 0, NULL, '2026-02-08 14:49:08', '2026-02-08 14:49:08'),
(26, 15, 2, 'asad', NULL, NULL, 0, NULL, '2026-02-08 14:53:49', '2026-02-08 14:53:49'),
(27, 16, 2, 'oi', NULL, NULL, 0, NULL, '2026-02-08 14:59:47', '2026-02-08 14:59:47'),
(28, 16, 2, 'ola', NULL, NULL, 0, NULL, '2026-02-08 23:51:52', '2026-02-08 23:51:52'),
(29, 16, 2, '', 'support/attachments/CdrQ78s7JRsAXfIYDXljtEXukOuXeM5k3BjjBj6r.png', 'Design sem nome (7).png', 0, NULL, '2026-02-08 23:51:56', '2026-02-08 23:51:56'),
(30, 16, 2, 'oi', NULL, NULL, 0, NULL, '2026-02-08 23:52:13', '2026-02-08 23:52:13');

-- --------------------------------------------------------

--
-- Estrutura da tabela `support_notifications`
--

CREATE TABLE `support_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ticket_id` bigint(20) UNSIGNED NOT NULL,
  `message_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `support_notifications`
--

INSERT INTO `support_notifications` (`id`, `ticket_id`, `message_id`, `user_id`, `is_read`, `read_at`, `created_at`, `updated_at`) VALUES
(12, 15, 26, 2, 1, '2026-02-08 14:55:24', '2026-02-08 14:53:49', '2026-02-08 14:55:24'),
(13, 16, 27, 2, 1, '2026-02-08 14:59:58', '2026-02-08 14:59:47', '2026-02-08 14:59:58'),
(14, 16, 28, 2, 1, '2026-02-08 23:52:08', '2026-02-08 23:51:52', '2026-02-08 23:52:08'),
(15, 16, 29, 2, 1, '2026-02-08 23:52:08', '2026-02-08 23:51:56', '2026-02-08 23:52:08');

-- --------------------------------------------------------

--
-- Estrutura da tabela `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `assigned_to` bigint(20) UNSIGNED DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('open','in_progress','waiting','resolved','closed') NOT NULL DEFAULT 'open',
  `priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `last_message_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `support_tickets`
--

INSERT INTO `support_tickets` (`id`, `user_id`, `assigned_to`, `subject`, `description`, `status`, `priority`, `last_message_at`, `created_at`, `updated_at`) VALUES
(13, 2, NULL, 'saldo bloqueado', 'dfasa', 'open', 'medium', '2026-02-08 14:43:26', '2026-02-08 14:43:26', '2026-02-08 14:43:26'),
(14, 2, NULL, 'ajuda saque', 'pf me ajuda', 'open', 'medium', '2026-02-08 14:49:08', '2026-02-08 14:49:08', '2026-02-08 14:49:08'),
(15, 2, NULL, 'sad', 'asad', 'open', 'medium', '2026-02-08 14:53:49', '2026-02-08 14:53:49', '2026-02-08 14:53:49'),
(16, 2, NULL, 'oi', 'oi', 'closed', 'medium', '2026-02-08 23:52:13', '2026-02-08 14:59:47', '2026-02-08 23:52:20');

-- --------------------------------------------------------

--
-- Estrutura da tabela `system_gateway_configs`
--

CREATE TABLE `system_gateway_configs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `provider_name` varchar(255) NOT NULL,
  `client_id` varchar(255) DEFAULT NULL,
  `client_secret` text DEFAULT NULL,
  `pix_key` varchar(255) DEFAULT NULL,
  `certificate_path` text DEFAULT NULL,
  `wallet_id` varchar(255) DEFAULT NULL,
  `is_active_for_pix` tinyint(1) NOT NULL DEFAULT 0,
  `is_default_for_pix` tinyint(1) NOT NULL DEFAULT 0,
  `is_active_for_card` tinyint(1) NOT NULL DEFAULT 0,
  `is_active_for_boleto` tinyint(1) NOT NULL DEFAULT 0,
  `is_default_for_card` tinyint(1) NOT NULL DEFAULT 0,
  `priority` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `system_gateway_configs`
--

INSERT INTO `system_gateway_configs` (`id`, `provider_name`, `client_id`, `client_secret`, `pix_key`, `certificate_path`, `wallet_id`, `is_active_for_pix`, `is_default_for_pix`, `is_active_for_card`, `is_active_for_boleto`, `is_default_for_card`, `priority`, `created_at`, `updated_at`) VALUES
(1, 'asaas', '0', '$0', NULL, NULL, NULL, 0, 0, 0, 0, 0, 1, '2025-11-24 21:21:05', '2026-01-06 15:13:55'),
(2, 'mercadopago', NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 1, '2025-11-24 21:21:05', '2025-11-24 21:58:52'),
(3, 'bspay', NULL, NULL, NULL, NULL, NULL, 0, 1, 0, 0, 0, 1, '2025-11-24 21:21:05', '2026-02-08 19:21:11'),
(4, 'pixup', NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 1, '2025-11-24 21:21:05', '2025-11-27 02:39:12'),
(5, 'efi', NULL, NULL, NULL, 'pem/producao-793314-BRL Gateway.p12', NULL, 0, 0, 0, 0, 0, 1, '2025-11-24 21:21:05', '2026-02-08 19:21:20'),
(6, 'ondapay', NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 1, '2025-11-24 21:21:05', '2025-11-24 21:58:52'),
(7, 'infinitypay', NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 1, '2025-11-24 21:21:05', '2025-11-24 21:21:05'),
(8, 'venit', NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, '2025-11-27 01:26:48', '2026-02-08 19:21:11'),
(9, 'pluggou', NULL, NULL, NULL, NULL, NULL, 1, 0, 0, 0, 0, 0, '2025-11-30 00:19:42', '2026-03-03 16:24:55'),
(10, 'podpay', NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, '2026-02-03 19:44:16', '2026-02-08 19:21:11'),
(11, 'hypercash', NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, '2026-02-03 19:44:16', '2026-03-02 20:21:59'),
(12, 'paguemax', NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, '2026-02-03 22:48:03', '2026-02-08 19:21:11'),
(13, 'zoompag', NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, '2026-02-08 17:16:57', '2026-03-02 20:21:06');

-- --------------------------------------------------------

--
-- Estrutura da tabela `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `amount_gross` decimal(15,2) NOT NULL,
  `amount_net` decimal(15,2) NOT NULL,
  `fee` decimal(15,2) NOT NULL DEFAULT 0.00,
  `type` enum('pix','credit','debit','credit_manual','advance') NOT NULL,
  `status` enum('pending','processing','completed','failed','cancelled','chargeback','mediation') DEFAULT 'pending',
  `paid_at` timestamp NULL DEFAULT NULL,
  `released_at` timestamp NULL DEFAULT NULL COMMENT 'Data em que o valor foi liberado para saque',
  `available_at` timestamp NULL DEFAULT NULL COMMENT 'Data em que o valor ficará disponível para saque (para cartão de crédito)',
  `gateway_provider` varchar(50) DEFAULT NULL,
  `external_id` varchar(255) DEFAULT NULL,
  `payer_name` varchar(255) DEFAULT NULL,
  `payer_email` varchar(255) DEFAULT NULL,
  `payer_cpf` varchar(14) DEFAULT NULL,
  `payer_phone` varchar(255) DEFAULT NULL,
  `payer_address` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `transactions`
--

INSERT INTO `transactions` (`id`, `uuid`, `user_id`, `product_id`, `amount_gross`, `amount_net`, `fee`, `type`, `status`, `paid_at`, `released_at`, `available_at`, `gateway_provider`, `external_id`, `payer_name`, `payer_email`, `payer_cpf`, `payer_phone`, `payer_address`, `description`, `created_at`, `updated_at`, `expires_at`) VALUES
(131, '7edf5f14-1e65-4b97-ae01-f1593f22cf63', 2, NULL, 58.00, 56.00, 2.00, 'pix', 'pending', NULL, NULL, NULL, 'podpay', '39923963', 'renato oliveira', 'djrdmonster@gmail.com', NULL, NULL, NULL, NULL, '2026-02-05 14:23:08', '2026-02-05 14:23:08', '2026-02-05 14:28:02'),
(132, 'cac0bc88-e75f-4c7e-9933-b8b987789ba0', 2, NULL, 59.00, 57.00, 2.00, 'pix', 'pending', NULL, NULL, NULL, 'efi', 'dfe476e99caf478c80034289bf9d5787', 'renato oliveira', 'djrdmonster@gmail.com', NULL, NULL, NULL, NULL, '2026-02-05 14:35:13', '2026-02-05 14:35:13', '2026-02-05 14:40:11'),
(133, '0f344525-8b5e-4ba8-b111-138893ff9594', 2, NULL, 36.00, 34.00, 2.00, 'pix', 'pending', NULL, NULL, NULL, 'venit', '514c5845-bbac-4dd7-8ef8-f64fe81eae4e', 'renato oliveira', 'djrdmonster@gmail.com', NULL, NULL, NULL, NULL, '2026-02-05 15:11:15', '2026-02-05 15:11:15', '2026-02-07 03:00:00'),
(134, '0d41c562-6c09-4283-8abb-b9271b886cf0', 2, NULL, 15.00, 13.00, 2.00, 'pix', 'pending', NULL, NULL, NULL, 'venit', '984ec1ab-1f54-416e-9a4b-dbb5b2008287', 'Renato oliveira', 'djrdmonster@gmail.com', NULL, NULL, NULL, NULL, '2026-02-08 17:19:22', '2026-02-08 17:19:22', '2026-02-10 03:00:00'),
(135, '387708ff-df5e-4b6a-a743-87645c2d7d1f', 2, NULL, 36.00, 34.00, 2.00, 'pix', 'pending', NULL, NULL, NULL, 'zoompag', '21ef1bd5-c739-4fe8-a0cd-a389dc478d6e', 'Renato oliveira', 'djrdmonster@gmail.com', NULL, NULL, NULL, NULL, '2026-02-08 18:14:11', '2026-02-08 18:14:11', '2026-02-08 18:19:09'),
(136, '97e60ba4-2024-4489-99ab-e0681cf9f830', 2, 15, 185.80, 183.80, 2.00, 'pix', 'completed', '2026-02-08 18:21:49', '2026-02-08 18:21:49', '2026-02-08 18:21:49', 'zoompag', '97e60ba4-2024-4489-99ab-e0681cf9f830', 'Marcio batista pinto', 'djrdmonster@gmail.com', '002.587.807-70', NULL, NULL, NULL, '2026-02-08 18:15:46', '2026-02-08 18:21:49', NULL),
(137, '002e919a-6e68-4501-8009-542d73168a55', 2, NULL, 85.00, 83.00, 2.00, 'pix', 'pending', NULL, NULL, NULL, 'pluggou', '0d8d4744-a2bf-4f68-811e-6579a46351cd', 'Renato oliveira', 'djrdmonster@gmail.com', NULL, NULL, NULL, NULL, '2026-02-08 19:20:05', '2026-02-08 19:20:05', '2026-02-08 19:25:04'),
(138, 'e2a2e07d-52ec-4db4-8894-2908563c24e2', 2, NULL, 65.00, 63.00, 2.00, 'pix', 'pending', NULL, NULL, NULL, 'pluggou', 'f4f991ee-1c11-424f-82a2-9629b511c21e', 'Renato oliveira', 'demo@demo.com', NULL, NULL, NULL, NULL, '2026-03-02 20:29:25', '2026-03-02 20:29:25', '2026-03-02 20:34:24'),
(139, 'b7723ce6-3eb1-4ea9-ad72-09eb047fa4cd', 2, 15, 185.80, 183.80, 2.00, 'pix', 'pending', NULL, NULL, NULL, 'pluggou', 'b7723ce6-3eb1-4ea9-ad72-09eb047fa4cd', 'Marcio batista pinto', 'djrdmonster@gmail.com', '002.587.807-70', NULL, NULL, NULL, '2026-03-02 20:40:59', '2026-03-02 20:40:59', NULL),
(140, 'e384507c-bff0-44a5-beb1-de8cfc0b9d35', 2, 15, 185.80, 183.80, 2.00, 'pix', 'pending', NULL, NULL, NULL, 'pluggou', 'e384507c-bff0-44a5-beb1-de8cfc0b9d35', 'Marcio batista pinto', 'djrdmonster@gmail.com', '002.587.807-70', NULL, NULL, NULL, '2026-03-03 16:06:15', '2026-03-03 16:06:15', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `transaction_splits`
--

CREATE TABLE `transaction_splits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `transaction_id` bigint(20) UNSIGNED NOT NULL,
  `payment_split_id` bigint(20) UNSIGNED NOT NULL,
  `recipient_user_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `split_type` enum('percentage','fixed') NOT NULL,
  `split_value` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `upsells`
--

CREATE TABLE `upsells` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `upsell_product_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(15,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `language` varchar(5) NOT NULL DEFAULT 'pt',
  `profile_photo` varchar(255) DEFAULT NULL,
  `cpf_cnpj` varchar(18) DEFAULT NULL,
  `person_type` enum('PF','PJ') DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `cep` varchar(10) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `address_number` varchar(255) DEFAULT NULL,
  `address_complement` varchar(255) DEFAULT NULL,
  `neighborhood` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(2) DEFAULT NULL,
  `zip_code` varchar(10) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `number` varchar(20) DEFAULT NULL,
  `monthly_billing` decimal(10,2) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `email_verified` tinyint(1) NOT NULL DEFAULT 0,
  `kyc_status` enum('pending','verified','approved','rejected') DEFAULT 'pending',
  `rejection_reason` text DEFAULT NULL,
  `kyc_step` int(11) NOT NULL DEFAULT 1,
  `documents_sent` tinyint(1) NOT NULL DEFAULT 0,
  `doc_front` varchar(255) DEFAULT NULL,
  `doc_back` varchar(255) DEFAULT NULL,
  `selfie_with_doc` varchar(255) DEFAULT NULL,
  `cnpj_card` varchar(255) DEFAULT NULL,
  `facial_biometrics` varchar(255) DEFAULT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT 1,
  `affiliate_code` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `pin` varchar(255) DEFAULT NULL,
  `google2fa_secret` text DEFAULT NULL,
  `google2fa_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `pin_configured` tinyint(1) NOT NULL DEFAULT 0,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `manager_id` bigint(20) UNSIGNED DEFAULT NULL,
  `preferred_gateway` varchar(255) DEFAULT NULL,
  `withdrawal_auto` tinyint(1) NOT NULL DEFAULT 1,
  `withdrawal_mode` enum('auto','manual') DEFAULT NULL COMMENT 'Modo de saque individual (auto/manual). Null = usa configuração global',
  `first_withdrawal_completed` tinyint(1) NOT NULL DEFAULT 0,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `is_manager` tinyint(1) NOT NULL DEFAULT 0,
  `taxa_entrada` decimal(5,2) NOT NULL DEFAULT 2.99,
  `taxa_entrada_fixo` decimal(10,2) DEFAULT NULL,
  `taxa_saida` decimal(5,2) NOT NULL DEFAULT 1.00,
  `taxa_saida_fixo` decimal(10,2) DEFAULT NULL,
  `taxa_extorno` decimal(5,2) NOT NULL DEFAULT 0.00,
  `split_fixed` decimal(10,2) DEFAULT 0.00,
  `split_variable` decimal(10,2) DEFAULT 0.00,
  `bloquear_saque` tinyint(1) NOT NULL DEFAULT 0,
  `is_blocked` tinyint(1) NOT NULL DEFAULT 0,
  `receive_marketing_emails` tinyint(1) NOT NULL DEFAULT 1,
  `last_email_sent_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `language`, `profile_photo`, `cpf_cnpj`, `person_type`, `birth_date`, `phone`, `cep`, `address`, `address_number`, `address_complement`, `neighborhood`, `city`, `state`, `zip_code`, `street`, `number`, `monthly_billing`, `email_verified_at`, `email_verified`, `kyc_status`, `rejection_reason`, `kyc_step`, `documents_sent`, `doc_front`, `doc_back`, `selfie_with_doc`, `cnpj_card`, `facial_biometrics`, `is_approved`, `affiliate_code`, `password`, `pin`, `google2fa_secret`, `google2fa_enabled`, `pin_configured`, `remember_token`, `created_at`, `updated_at`, `manager_id`, `preferred_gateway`, `withdrawal_auto`, `withdrawal_mode`, `first_withdrawal_completed`, `is_admin`, `is_manager`, `taxa_entrada`, `taxa_entrada_fixo`, `taxa_saida`, `taxa_saida_fixo`, `taxa_extorno`, `split_fixed`, `split_variable`, `bloquear_saque`, `is_blocked`, `receive_marketing_emails`, `last_email_sent_at`) VALUES
(2, 'Pagamentos Online LTDA', 'demo@demo.com', 'pt', 'IMG/profile/profile_2_1764540416_692cc000026ba.png', '17105396741', NULL, '1998-08-22', '21959396216', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 30000.00, NULL, 0, 'approved', NULL, 1, 0, NULL, NULL, NULL, NULL, NULL, 1, 'FE23F381', '$2y$12$moaJXuW732GfXNTiu..DC.BYg5GcTcEc6n2H9JVDO9RUP9hVnczQ2', '$2y$12$z88SNADSfI7v6YmfXyrVM.H/tZ5SordBnRQn0o63rXuE2VLwtPZSu', 'HFSPQIOMYIAU2H7Q', 0, 1, 'w7puJK03eua4VnBKKdllzB829ocv3SXLKVzHLAGL2xwES0H6wb6W1zdl5atf', '2025-11-24 04:43:24', '2026-02-09 21:37:04', NULL, NULL, 0, 'auto', 1, 1, 0, 0.00, NULL, 0.00, NULL, 4.00, 0.00, 0.00, 0, 0, 1, '2026-01-10 22:21:31');

-- --------------------------------------------------------

--
-- Estrutura da tabela `user_notifications`
--

CREATE TABLE `user_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `notification_id` bigint(20) UNSIGNED NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `is_pushed` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `wallets`
--

CREATE TABLE `wallets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `accumulated_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `frozen_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `negative_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `wallets`
--

INSERT INTO `wallets` (`id`, `user_id`, `balance`, `accumulated_balance`, `frozen_balance`, `negative_balance`, `created_at`, `updated_at`) VALUES
(1, 2, 221.80, 183.80, 0.00, 0.00, '2025-11-24 04:43:24', '2026-02-08 19:00:23');

-- --------------------------------------------------------

--
-- Estrutura da tabela `webhooks`
--

CREATE TABLE `webhooks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `secret` varchar(255) DEFAULT NULL,
  `events` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`events`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `retry_attempts` int(11) NOT NULL DEFAULT 3,
  `timeout_seconds` int(11) NOT NULL DEFAULT 30,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `webhook_logs`
--

CREATE TABLE `webhook_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `webhook_id` bigint(20) UNSIGNED NOT NULL,
  `event` varchar(255) NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`payload`)),
  `response_status` int(11) DEFAULT NULL,
  `response_body` text DEFAULT NULL,
  `status` enum('pending','success','failed','retrying') NOT NULL DEFAULT 'pending',
  `attempts` int(11) NOT NULL DEFAULT 0,
  `error_message` text DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `withdrawals`
--

CREATE TABLE `withdrawals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `amount_gross` decimal(15,2) DEFAULT NULL,
  `fee` decimal(15,2) NOT NULL DEFAULT 0.00,
  `pix_key` varchar(255) NOT NULL,
  `external_id` varchar(255) DEFAULT NULL,
  `status` enum('pending','processing','completed','paid','failed','cancelled') DEFAULT 'pending',
  `proof_url` varchar(255) DEFAULT NULL,
  `admin_note` text DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `withdrawals`
--

INSERT INTO `withdrawals` (`id`, `user_id`, `amount`, `amount_gross`, `fee`, `pix_key`, `external_id`, `status`, `proof_url`, `admin_note`, `processed_at`, `created_at`, `updated_at`) VALUES
(32, 2, 3.00, 5.00, 2.00, 'contaveo3barth1@gmail.com', NULL, 'cancelled', NULL, NULL, NULL, '2026-02-08 18:17:38', '2026-02-08 19:00:23'),
(33, 2, 50.00, 50.00, 0.00, 'test@pix.com', 'withdraw_1770574957', 'paid', NULL, NULL, '2026-02-08 18:22:38', '2026-02-08 18:22:37', '2026-02-08 18:22:38'),
(34, 2, 3.00, 5.00, 2.00, 'contaveo3barth1@gmail.com', NULL, 'cancelled', NULL, NULL, NULL, '2026-02-08 18:34:54', '2026-02-08 19:00:20');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `abandoned_carts`
--
ALTER TABLE `abandoned_carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `abandoned_carts_user_id_foreign` (`user_id`),
  ADD KEY `abandoned_carts_product_id_foreign` (`product_id`),
  ADD KEY `abandoned_carts_email_session_id_index` (`email`,`session_id`),
  ADD KEY `abandoned_carts_recovered_at_index` (`recovered_at`);

--
-- Índices para tabela `api_tokens`
--
ALTER TABLE `api_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `api_tokens_token_unique` (`token`),
  ADD UNIQUE KEY `api_tokens_client_id_unique` (`client_id`),
  ADD KEY `api_tokens_user_id_is_active_index` (`user_id`,`is_active`),
  ADD KEY `api_tokens_token_index` (`token`);

--
-- Índices para tabela `api_token_allowed_ips`
--
ALTER TABLE `api_token_allowed_ips`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `api_token_allowed_ips_api_token_id_ip_address_unique` (`api_token_id`,`ip_address`);

--
-- Índices para tabela `app_connections`
--
ALTER TABLE `app_connections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `app_connections_user_id_app_id_unique` (`user_id`,`app_id`),
  ADD KEY `app_connections_user_id_is_active_index` (`user_id`,`is_active`);

--
-- Índices para tabela `awards`
--
ALTER TABLE `awards`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Índices para tabela `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Índices para tabela `chargebacks`
--
ALTER TABLE `chargebacks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chargebacks_transaction_id_foreign` (`transaction_id`),
  ADD KEY `chargebacks_user_id_foreign` (`user_id`);

--
-- Índices para tabela `checkout_sales`
--
ALTER TABLE `checkout_sales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `checkout_sales_uuid_unique` (`uuid`),
  ADD KEY `checkout_sales_product_id_foreign` (`product_id`),
  ADD KEY `checkout_sales_user_id_status_index` (`user_id`,`status`),
  ADD KEY `checkout_sales_uuid_index` (`uuid`),
  ADD KEY `checkout_sales_external_ref_index` (`external_ref`),
  ADD KEY `checkout_sales_created_at_index` (`created_at`);

--
-- Índices para tabela `checkout_settings`
--
ALTER TABLE `checkout_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `checkout_settings_user_id_unique` (`user_id`);

--
-- Índices para tabela `checkout_user_settings`
--
ALTER TABLE `checkout_user_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `checkout_user_settings_user_id_unique` (`user_id`);

--
-- Índices para tabela `checkout_webhooks`
--
ALTER TABLE `checkout_webhooks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `checkout_webhooks_user_id_is_active_index` (`user_id`,`is_active`);

--
-- Índices para tabela `checkout_webhook_logs`
--
ALTER TABLE `checkout_webhook_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `checkout_webhook_logs_webhook_id_status_index` (`webhook_id`,`status`),
  ADD KEY `checkout_webhook_logs_status_created_at_index` (`status`,`created_at`);

--
-- Índices para tabela `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `coupons_code_unique` (`code`),
  ADD KEY `coupons_user_id_foreign` (`user_id`),
  ADD KEY `coupons_code_is_active_index` (`code`,`is_active`);

--
-- Índices para tabela `email_campaigns`
--
ALTER TABLE `email_campaigns`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `email_logs`
--
ALTER TABLE `email_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email_logs_campaign_id_foreign` (`campaign_id`),
  ADD KEY `email_logs_user_id_type_index` (`user_id`,`type`),
  ADD KEY `email_logs_status_index` (`status`),
  ADD KEY `email_logs_created_at_index` (`created_at`);

--
-- Índices para tabela `email_templates`
--
ALTER TABLE `email_templates`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `error_logs`
--
ALTER TABLE `error_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `error_logs_level_index` (`level`),
  ADD KEY `error_logs_type_index` (`type`),
  ADD KEY `error_logs_resolved_index` (`resolved`),
  ADD KEY `error_logs_created_at_index` (`created_at`);

--
-- Índices para tabela `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Índices para tabela `integrations`
--
ALTER TABLE `integrations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `integrations_user_id_platform_is_active_index` (`user_id`,`platform`,`is_active`),
  ADD KEY `integrations_platform_index` (`platform`);

--
-- Índices para tabela `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Índices para tabela `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `landing_page_settings`
--
ALTER TABLE `landing_page_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `landing_page_settings_key_unique` (`key`),
  ADD KEY `landing_page_settings_key_index` (`key`);

--
-- Índices para tabela `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_is_active_index` (`is_active`),
  ADD KEY `notifications_starts_at_ends_at_index` (`starts_at`,`ends_at`);

--
-- Índices para tabela `order_bumps`
--
ALTER TABLE `order_bumps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_bumps_product_id_foreign` (`product_id`);

--
-- Índices para tabela `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Índices para tabela `payment_splits`
--
ALTER TABLE `payment_splits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_splits_user_id_is_active_index` (`user_id`,`is_active`),
  ADD KEY `payment_splits_recipient_user_id_index` (`recipient_user_id`);

--
-- Índices para tabela `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_uuid_unique` (`uuid`),
  ADD KEY `products_order_bump_product_id_foreign` (`order_bump_product_id`),
  ADD KEY `products_user_id_is_active_index` (`user_id`,`is_active`),
  ADD KEY `products_uuid_index` (`uuid`);

--
-- Índices para tabela `product_order_bumps`
--
ALTER TABLE `product_order_bumps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_order_bumps_bump_product_id_foreign` (`bump_product_id`),
  ADD KEY `product_order_bumps_product_id_is_active_index` (`product_id`,`is_active`);

--
-- Índices para tabela `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Índices para tabela `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Índices para tabela `smtp_settings`
--
ALTER TABLE `smtp_settings`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `static_pages`
--
ALTER TABLE `static_pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `static_pages_slug_unique` (`slug`);

--
-- Índices para tabela `support_messages`
--
ALTER TABLE `support_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `support_messages_user_id_foreign` (`user_id`),
  ADD KEY `support_messages_ticket_id_index` (`ticket_id`),
  ADD KEY `support_messages_is_read_index` (`is_read`);

--
-- Índices para tabela `support_notifications`
--
ALTER TABLE `support_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `support_notifications_message_id_foreign` (`message_id`),
  ADD KEY `support_notifications_user_id_is_read_index` (`user_id`,`is_read`),
  ADD KEY `support_notifications_ticket_id_index` (`ticket_id`);

--
-- Índices para tabela `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `support_tickets_assigned_to_foreign` (`assigned_to`),
  ADD KEY `support_tickets_status_index` (`status`),
  ADD KEY `support_tickets_user_id_index` (`user_id`);

--
-- Índices para tabela `system_gateway_configs`
--
ALTER TABLE `system_gateway_configs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `system_gateway_configs_provider_name_unique` (`provider_name`),
  ADD KEY `system_gateway_configs_is_active_for_pix_priority_index` (`is_active_for_pix`,`priority`),
  ADD KEY `system_gateway_configs_is_active_for_card_priority_index` (`is_active_for_card`,`priority`),
  ADD KEY `system_gateway_configs_is_default_for_pix_index` (`is_default_for_pix`),
  ADD KEY `system_gateway_configs_is_default_for_card_index` (`is_default_for_card`);

--
-- Índices para tabela `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transactions_uuid_unique` (`uuid`),
  ADD KEY `transactions_user_id_status_index` (`user_id`,`status`),
  ADD KEY `transactions_uuid_index` (`uuid`),
  ADD KEY `transactions_external_id_index` (`external_id`),
  ADD KEY `transactions_payer_email_index` (`payer_email`),
  ADD KEY `transactions_product_id_index` (`product_id`),
  ADD KEY `transactions_status_available_at_index` (`status`,`available_at`),
  ADD KEY `transactions_type_available_at_index` (`type`,`available_at`),
  ADD KEY `transactions_user_id_status_created_at_index` (`user_id`,`status`,`created_at`),
  ADD KEY `transactions_user_id_status_type_index` (`user_id`,`status`,`type`),
  ADD KEY `transactions_user_id_created_at_index` (`user_id`,`created_at`);

--
-- Índices para tabela `transaction_splits`
--
ALTER TABLE `transaction_splits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_splits_payment_split_id_foreign` (`payment_split_id`),
  ADD KEY `transaction_splits_transaction_id_index` (`transaction_id`),
  ADD KEY `transaction_splits_recipient_user_id_index` (`recipient_user_id`);

--
-- Índices para tabela `upsells`
--
ALTER TABLE `upsells`
  ADD PRIMARY KEY (`id`),
  ADD KEY `upsells_product_id_foreign` (`product_id`),
  ADD KEY `upsells_upsell_product_id_foreign` (`upsell_product_id`);

--
-- Índices para tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_affiliate_code_unique` (`affiliate_code`),
  ADD KEY `users_manager_id_foreign` (`manager_id`);

--
-- Índices para tabela `user_notifications`
--
ALTER TABLE `user_notifications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_notifications_user_id_notification_id_unique` (`user_id`,`notification_id`),
  ADD KEY `user_notifications_notification_id_foreign` (`notification_id`),
  ADD KEY `user_notifications_is_read_index` (`is_read`);

--
-- Índices para tabela `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `wallets_user_id_unique` (`user_id`);

--
-- Índices para tabela `webhooks`
--
ALTER TABLE `webhooks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `webhooks_user_id_is_active_index` (`user_id`,`is_active`);

--
-- Índices para tabela `webhook_logs`
--
ALTER TABLE `webhook_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `webhook_logs_webhook_id_status_index` (`webhook_id`,`status`),
  ADD KEY `webhook_logs_status_created_at_index` (`status`,`created_at`);

--
-- Índices para tabela `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `withdrawals_user_id_status_index` (`user_id`,`status`),
  ADD KEY `withdrawals_external_id_index` (`external_id`),
  ADD KEY `withdrawals_user_id_status_created_at_index` (`user_id`,`status`,`created_at`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `abandoned_carts`
--
ALTER TABLE `abandoned_carts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `api_tokens`
--
ALTER TABLE `api_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `api_token_allowed_ips`
--
ALTER TABLE `api_token_allowed_ips`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `app_connections`
--
ALTER TABLE `app_connections`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `awards`
--
ALTER TABLE `awards`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `chargebacks`
--
ALTER TABLE `chargebacks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `checkout_sales`
--
ALTER TABLE `checkout_sales`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT de tabela `checkout_settings`
--
ALTER TABLE `checkout_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `checkout_user_settings`
--
ALTER TABLE `checkout_user_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `checkout_webhooks`
--
ALTER TABLE `checkout_webhooks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `checkout_webhook_logs`
--
ALTER TABLE `checkout_webhook_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `email_campaigns`
--
ALTER TABLE `email_campaigns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `email_logs`
--
ALTER TABLE `email_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT de tabela `email_templates`
--
ALTER TABLE `email_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `error_logs`
--
ALTER TABLE `error_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT de tabela `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `integrations`
--
ALTER TABLE `integrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `landing_page_settings`
--
ALTER TABLE `landing_page_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT de tabela `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT de tabela `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de tabela `order_bumps`
--
ALTER TABLE `order_bumps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `payment_splits`
--
ALTER TABLE `payment_splits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `product_order_bumps`
--
ALTER TABLE `product_order_bumps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT de tabela `smtp_settings`
--
ALTER TABLE `smtp_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `static_pages`
--
ALTER TABLE `static_pages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `support_messages`
--
ALTER TABLE `support_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de tabela `support_notifications`
--
ALTER TABLE `support_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `system_gateway_configs`
--
ALTER TABLE `system_gateway_configs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=141;

--
-- AUTO_INCREMENT de tabela `transaction_splits`
--
ALTER TABLE `transaction_splits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `upsells`
--
ALTER TABLE `upsells`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de tabela `user_notifications`
--
ALTER TABLE `user_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=196;

--
-- AUTO_INCREMENT de tabela `wallets`
--
ALTER TABLE `wallets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de tabela `webhooks`
--
ALTER TABLE `webhooks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `webhook_logs`
--
ALTER TABLE `webhook_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `withdrawals`
--
ALTER TABLE `withdrawals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `abandoned_carts`
--
ALTER TABLE `abandoned_carts`
  ADD CONSTRAINT `abandoned_carts_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `abandoned_carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `api_tokens`
--
ALTER TABLE `api_tokens`
  ADD CONSTRAINT `api_tokens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `api_token_allowed_ips`
--
ALTER TABLE `api_token_allowed_ips`
  ADD CONSTRAINT `api_token_allowed_ips_api_token_id_foreign` FOREIGN KEY (`api_token_id`) REFERENCES `api_tokens` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `app_connections`
--
ALTER TABLE `app_connections`
  ADD CONSTRAINT `app_connections_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `chargebacks`
--
ALTER TABLE `chargebacks`
  ADD CONSTRAINT `chargebacks_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chargebacks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `checkout_sales`
--
ALTER TABLE `checkout_sales`
  ADD CONSTRAINT `checkout_sales_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `checkout_sales_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `checkout_settings`
--
ALTER TABLE `checkout_settings`
  ADD CONSTRAINT `checkout_settings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `checkout_user_settings`
--
ALTER TABLE `checkout_user_settings`
  ADD CONSTRAINT `checkout_user_settings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `checkout_webhooks`
--
ALTER TABLE `checkout_webhooks`
  ADD CONSTRAINT `checkout_webhooks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `checkout_webhook_logs`
--
ALTER TABLE `checkout_webhook_logs`
  ADD CONSTRAINT `checkout_webhook_logs_webhook_id_foreign` FOREIGN KEY (`webhook_id`) REFERENCES `checkout_webhooks` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `coupons`
--
ALTER TABLE `coupons`
  ADD CONSTRAINT `coupons_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `email_logs`
--
ALTER TABLE `email_logs`
  ADD CONSTRAINT `email_logs_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `email_campaigns` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `email_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `integrations`
--
ALTER TABLE `integrations`
  ADD CONSTRAINT `integrations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `order_bumps`
--
ALTER TABLE `order_bumps`
  ADD CONSTRAINT `order_bumps_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `payment_splits`
--
ALTER TABLE `payment_splits`
  ADD CONSTRAINT `payment_splits_recipient_user_id_foreign` FOREIGN KEY (`recipient_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payment_splits_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_order_bump_product_id_foreign` FOREIGN KEY (`order_bump_product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `product_order_bumps`
--
ALTER TABLE `product_order_bumps`
  ADD CONSTRAINT `product_order_bumps_bump_product_id_foreign` FOREIGN KEY (`bump_product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_order_bumps_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `support_messages`
--
ALTER TABLE `support_messages`
  ADD CONSTRAINT `support_messages_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `support_messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `support_notifications`
--
ALTER TABLE `support_notifications`
  ADD CONSTRAINT `support_notifications_message_id_foreign` FOREIGN KEY (`message_id`) REFERENCES `support_messages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `support_notifications_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `support_notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD CONSTRAINT `support_tickets_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `support_tickets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `transaction_splits`
--
ALTER TABLE `transaction_splits`
  ADD CONSTRAINT `transaction_splits_payment_split_id_foreign` FOREIGN KEY (`payment_split_id`) REFERENCES `payment_splits` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaction_splits_recipient_user_id_foreign` FOREIGN KEY (`recipient_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaction_splits_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `upsells`
--
ALTER TABLE `upsells`
  ADD CONSTRAINT `upsells_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `upsells_upsell_product_id_foreign` FOREIGN KEY (`upsell_product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `user_notifications`
--
ALTER TABLE `user_notifications`
  ADD CONSTRAINT `user_notifications_notification_id_foreign` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `wallets`
--
ALTER TABLE `wallets`
  ADD CONSTRAINT `wallets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `webhooks`
--
ALTER TABLE `webhooks`
  ADD CONSTRAINT `webhooks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `webhook_logs`
--
ALTER TABLE `webhook_logs`
  ADD CONSTRAINT `webhook_logs_webhook_id_foreign` FOREIGN KEY (`webhook_id`) REFERENCES `webhooks` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD CONSTRAINT `withdrawals_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

<?php

namespace App\Services\Gateways;

use App\Models\SystemGatewayConfig;
use InvalidArgumentException;

class GatewayFactory
{
    /**
     * Cria uma instância do gateway baseado no nome do provedor
     *
     * @param string $provider Nome do provedor (bspay, venit, podpay, hypercash, efi)
     * @return PaymentGatewayInterface
     * @throws InvalidArgumentException
     */
    public static function make(string $provider, ?string $clientId = null, ?string $clientSecret = null): PaymentGatewayInterface
    {
        $normalizedProvider = strtolower($provider);

        if ($normalizedProvider === 'efi') {
            // Busca configurações adicionais necessárias para Efí
            $config = SystemGatewayConfig::where('provider_name', 'efi')->first();
            
            $extraConfig = [
                'certificate_path' => $config->certificate_path ?? null,
                'pix_key' => $config->pix_key ?? null,
            ];

            return new EfiGateway($clientId, $clientSecret, $extraConfig);
        }

        return match ($normalizedProvider) {
            'bspay' => new BsPayGateway($clientId, $clientSecret),
            'venit' => new VenitGateway($clientId, $clientSecret), // IMPORTANTE: client_id = Secret Key, client_secret = Company ID
            'podpay' => new PodPayGateway($clientId, $clientSecret), // client_id = Public Key, client_secret = Secret Key
            'hypercash' => new HyperCashGateway($clientId), // client_id = API Token
            'paguemax' => new PagueMaxGateway($clientId, $clientSecret), // client_id + token_secret
            'zoompag' => new ZoomPagGateway($clientSecret), // client_secret = API Key (client_id ignored)
            'pluggou' => new PluggouGateway($clientId, $clientSecret), // client_id = Public Key, client_secret = Secret Key
            'efi' => new EfiGateway($clientId, $clientSecret), // Fallback se passar pelo if acima (não deve ocorrer, mas por segurança)
            default => throw new InvalidArgumentException("Gateway '{$provider}' não é suportado. Gateways disponíveis: bspay, venit, podpay, hypercash, efi, paguemax, zoompag, pluggou"),
        };
    }
}



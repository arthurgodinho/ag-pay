<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class SystemGatewayConfig extends Model
{
    protected $fillable = [
        'provider_name',
        'client_id',
        'client_secret',
        'pix_key',
        'certificate_path',
        'wallet_id',
        'is_active_for_pix',
        'is_active_for_card',
        'is_active_for_boleto',
        'is_default_for_pix',
        'is_default_for_card',
        'priority',
    ];

    protected function casts(): array
    {
        return [
            'is_active_for_pix' => 'boolean',
            'is_active_for_card' => 'boolean',
            'is_active_for_boleto' => 'boolean',
            'is_default_for_pix' => 'boolean',
            'is_default_for_card' => 'boolean',
            'priority' => 'integer',
        ];
    }

    /**
     * Busca o gateway padrão para PIX
     * Se não houver padrão, busca o ativo com maior prioridade
     * 
     * @param string|null $userPreferredGateway Gateway preferido do usuário
     * @return SystemGatewayConfig|null
     */
    public static function getDefaultForPix(?string $userPreferredGateway = null): ?self
    {
        // PRIORIDADE 1: Gateway padrão específico para recebimentos PIX
        $defaultForPix = \App\Models\Setting::get('default_gateway_for_pix', '');
        if (!empty($defaultForPix)) {
            $gateway = self::where('provider_name', $defaultForPix)
                ->where('is_active_for_pix', true)
                ->where(function($query) {
                    $query->where(function($q) {
                        $q->whereNotNull('client_id')->where('client_id', '!=', '');
                    })->orWhereIn('provider_name', ['hypercash', 'zoompag', 'pagarme']);
                })
                ->first();
            
            if ($gateway) {
                \Log::info('SystemGatewayConfig::getDefaultForPix: Usando gateway padrão para recebimentos PIX', [
                    'gateway' => $defaultForPix,
                    'gateway_id' => $gateway->id,
                ]);
                return $gateway;
            }
        }

        // PRIORIDADE 2: Gateway padrão global configurado pelo admin (compatibilidade)
        $globalDefault = \App\Models\Setting::get('default_gateway_for_all_users', '');
        if (!empty($globalDefault)) {
            $globalGateway = self::where('provider_name', $globalDefault)
                ->where('is_active_for_pix', true)
                ->where(function($query) {
                    $query->where(function($q) {
                        $q->whereNotNull('client_id')->where('client_id', '!=', '');
                    })->orWhereIn('provider_name', ['hypercash', 'zoompag', 'pagarme']);
                })
                ->first();
            
            if ($globalGateway) {
                \Log::info('SystemGatewayConfig::getDefaultForPix: Usando gateway padrão global', [
                    'gateway' => $globalDefault,
                    'gateway_id' => $globalGateway->id,
                ]);
                return $globalGateway;
            }
        }
        
        // PRIORIDADE 3: Gateway preferido do usuário (se não há gateway padrão global)
        if (!empty($userPreferredGateway)) {
            $userGateway = self::where('provider_name', $userPreferredGateway)
                ->where('is_active_for_pix', true)
                ->where(function($query) {
                    $query->where(function($q) {
                        $q->whereNotNull('client_id')->where('client_id', '!=', '');
                    })->orWhereIn('provider_name', ['hypercash', 'zoompag', 'pagarme']);
                })
                ->first();
            
            if ($userGateway) {
                \Log::info('SystemGatewayConfig::getDefaultForPix: Usando gateway preferido do usuário', [
                    'gateway' => $userPreferredGateway,
                    'gateway_id' => $userGateway->id,
                ]);
                return $userGateway;
            }
        }

        // PRIORIDADE 4: Gateway marcado como padrão do sistema (is_default_for_pix)
        $default = self::where('is_active_for_pix', true)
            ->where('is_default_for_pix', true)
            ->where(function($query) {
                $query->where(function($q) {
                    $q->whereNotNull('client_id')->where('client_id', '!=', '');
                })->orWhereIn('provider_name', ['hypercash', 'zoompag']);
            })
            ->first();

        if ($default) {
            return $default;
        }

        // PRIORIDADE 5: Gateway ativo com maior prioridade
        return self::where('is_active_for_pix', true)
            ->where(function($query) {
                $query->where(function($q) {
                    $q->whereNotNull('client_id')->where('client_id', '!=', '');
                })->orWhereIn('provider_name', ['hypercash', 'zoompag']);
            })
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Busca o gateway padrão para Depósitos/Cash-in PIX
     * 
     * @param string|null $userPreferredGateway Gateway preferido do usuário
     * @return SystemGatewayConfig|null
     */
    public static function getDefaultForCashinPix(?string $userPreferredGateway = null): ?self
    {
        // PRIORIDADE 1: Gateway padrão específico para cash-in PIX
        $defaultForCashinPix = \App\Models\Setting::get('default_gateway_for_cashin_pix', '');
        if (!empty($defaultForCashinPix)) {
            $gateway = self::where('provider_name', $defaultForCashinPix)
                ->where('is_active_for_pix', true)
                ->where(function($query) {
                    $query->where(function($q) {
                        $q->whereNotNull('client_id')->where('client_id', '!=', '');
                    })->orWhereIn('provider_name', ['hypercash', 'zoompag', 'pagarme']);
                })
                ->first();
            
            if ($gateway) {
                \Log::info('SystemGatewayConfig::getDefaultForCashinPix: Usando gateway padrão para cash-in PIX', [
                    'gateway' => $defaultForCashinPix,
                    'gateway_id' => $gateway->id,
                ]);
                return $gateway;
            }
        }

        // PRIORIDADE 2: Gateway padrão para recebimentos PIX (compatibilidade)
        return self::getDefaultForPix($userPreferredGateway);
    }

    /**
     * Busca o gateway padrão para Saques PIX
     * 
     * @param string|null $userPreferredGateway Gateway preferido do usuário
     * @return SystemGatewayConfig|null
     */
    public static function getDefaultForWithdrawals(?string $userPreferredGateway = null): ?self
    {
        // PRIORIDADE 1: Gateway padrão específico para saques PIX
        $defaultForWithdrawals = \App\Models\Setting::get('default_gateway_for_withdrawals', '');
        if (!empty($defaultForWithdrawals)) {
            $gateway = self::where('provider_name', $defaultForWithdrawals)
                ->where('is_active_for_pix', true)
                ->where(function($query) {
                    $query->where(function($q) {
                        $q->whereNotNull('client_id')->where('client_id', '!=', '');
                    })->orWhereIn('provider_name', ['hypercash', 'zoompag', 'pagarme']);
                })
                ->first();
            
            if ($gateway) {
                \Log::info('SystemGatewayConfig::getDefaultForWithdrawals: Usando gateway padrão para saques PIX', [
                    'gateway' => $defaultForWithdrawals,
                    'gateway_id' => $gateway->id,
                ]);
                return $gateway;
            }
        }

        // PRIORIDADE 2: Gateway padrão para recebimentos PIX (pode ser o mesmo)
        return self::getDefaultForPix($userPreferredGateway);
    }

    /**
     * Busca o gateway padrão para Cartão
     * Se não houver padrão, busca o ativo com maior prioridade
     *
     * @return SystemGatewayConfig|null
     */
    public static function getDefaultForCard(): ?self
    {
        // PRIORIDADE 1: Gateway padrão específico para cartão
        $defaultForCard = \App\Models\Setting::get('default_gateway_for_card', '');
        if (!empty($defaultForCard)) {
            $gateway = self::where('provider_name', $defaultForCard)
                ->where('is_active_for_card', true)
                ->where(function($query) {
                    $query->where(function($q) {
                        $q->whereNotNull('client_id')->where('client_id', '!=', '');
                    })->orWhereIn('provider_name', ['hypercash', 'zoompag', 'pagarme']);
                })
                ->whereNotNull('client_secret')
                ->where('client_secret', '!=', '')
                ->first();
            
            if ($gateway) {
                \Log::info('SystemGatewayConfig::getDefaultForCard: Usando gateway padrão para cartão', [
                    'gateway' => $defaultForCard,
                    'gateway_id' => $gateway->id,
                ]);
                return $gateway;
            }
        }

        // PRIORIDADE 2: Gateway padrão global (compatibilidade)
        $globalDefault = \App\Models\Setting::get('default_gateway_for_all_users', '');
        if (!empty($globalDefault)) {
            $globalGateway = self::where('provider_name', $globalDefault)
                ->where('is_active_for_card', true)
                ->where(function($query) {
                    $query->where(function($q) {
                        $q->whereNotNull('client_id')->where('client_id', '!=', '');
                    })->orWhereIn('provider_name', ['hypercash', 'zoompag', 'pagarme']);
                })
                ->whereNotNull('client_secret')
                ->where('client_secret', '!=', '')
                ->first();
            
            if ($globalGateway) {
                return $globalGateway;
            }
        }

        // PRIORIDADE 3: Gateway marcado como padrão do sistema (is_default_for_card)
        $default = self::where('is_active_for_card', true)
            ->where('is_default_for_card', true)
            ->where(function($query) {
                $query->where(function($q) {
                    $q->whereNotNull('client_id')->where('client_id', '!=', '');
                })->orWhereIn('provider_name', ['hypercash', 'zoompag', 'pagarme']);
            })
            ->whereNotNull('client_secret')
            ->where('client_secret', '!=', '')
            ->first();

        if ($default) {
            return $default;
        }

        // PRIORIDADE 4: Gateway ativo com maior prioridade
        return self::where('is_active_for_card', true)
            ->where(function($query) {
                $query->where(function($q) {
                    $q->whereNotNull('client_id')->where('client_id', '!=', '');
                })->orWhereIn('provider_name', ['hypercash', 'zoompag', 'pagarme']);
            })
            ->whereNotNull('client_secret')
            ->where('client_secret', '!=', '')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Busca o gateway ativo para PIX com maior prioridade
     * (Mantido para compatibilidade - usa getDefaultForPix)
     * 
     * @param string|null $userPreferredGateway Gateway preferido do usuário
     * @return SystemGatewayConfig|null
     */
    public static function getActiveForPix(?string $userPreferredGateway = null): ?self
    {
        return self::getDefaultForPix($userPreferredGateway);
    }

    /**
     * Busca o gateway ativo para Cartão com maior prioridade
     * (Mantido para compatibilidade - usa getDefaultForCard)
     *
     * @return SystemGatewayConfig|null
     */
    public static function getActiveForCard(): ?self
    {
        return self::getDefaultForCard();
    }

    /**
     * Busca o gateway padrão para Checkout (PIX)
     * Se não houver específico para checkout, usa o padrão de depósitos PIX
     * 
     * @return SystemGatewayConfig|null
     */
    public static function getDefaultForCheckoutPix(): ?self
    {
        // PRIORIDADE 1: Gateway padrão específico para checkout PIX
        $defaultForCheckoutPix = \App\Models\Setting::get('default_gateway_for_checkout_pix', '');
        if (!empty($defaultForCheckoutPix)) {
            $gateway = self::where('provider_name', $defaultForCheckoutPix)
                ->where('is_active_for_pix', true)
                ->where(function($query) {
                    $query->where(function($q) {
                        $q->whereNotNull('client_id')->where('client_id', '!=', '');
                    })->orWhereIn('provider_name', ['hypercash', 'zoompag', 'pagarme']);
                })
                ->first();
            
            if ($gateway) {
                \Log::info('SystemGatewayConfig::getDefaultForCheckoutPix: Usando gateway padrão para checkout PIX', [
                    'gateway' => $defaultForCheckoutPix,
                    'gateway_id' => $gateway->id,
                ]);
                return $gateway;
            }
        }

        // PRIORIDADE 2: Usa o gateway padrão de depósitos PIX
        return self::getDefaultForPix();
    }

    /**
     * Busca o gateway padrão para Checkout (Cartão)
     * Se não houver específico para checkout, usa o padrão de cartão geral
     * 
     * @return SystemGatewayConfig|null
     */
    public static function getDefaultForCheckoutCard(): ?self
    {
        // PRIORIDADE 1: Gateway padrão específico para checkout Cartão
        $defaultForCheckoutCard = \App\Models\Setting::get('default_gateway_for_checkout_card', '');
        if (!empty($defaultForCheckoutCard)) {
            $gateway = self::where('provider_name', $defaultForCheckoutCard)
                ->where('is_active_for_card', true)
                ->whereNotNull('client_id')
                ->whereNotNull('client_secret')
                ->where('client_id', '!=', '')
                ->where('client_secret', '!=', '')
                ->first();
            
            if ($gateway) {
                \Log::info('SystemGatewayConfig::getDefaultForCheckoutCard: Usando gateway padrão para checkout Cartão', [
                    'gateway' => $defaultForCheckoutCard,
                    'gateway_id' => $gateway->id,
                ]);
                return $gateway;
            }
        }

        // PRIORIDADE 2: Usa o gateway padrão de cartão geral
        return self::getDefaultForCard();
    }

    /**
     * Define este gateway como padrão para PIX
     * Remove o padrão de outros gateways
     *
     * @return void
     */
    public function setAsDefaultForPix(): void
    {
        // Remove padrão de outros gateways
        self::where('id', '!=', $this->id)
            ->where('is_default_for_pix', true)
            ->update(['is_default_for_pix' => false]);

        // Define este como padrão
        $this->update([
            'is_default_for_pix' => true,
            'is_active_for_pix' => true, // Garante que está ativo
        ]);
    }
}



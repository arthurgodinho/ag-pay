<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Setting;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'language',
        'password',
        'pin',
        'profile_photo',
        'cpf_cnpj',
        'birth_date',
        'phone',
        'monthly_billing',
        'kyc_status',
        'documents_sent',
        'is_approved',
        'pin_configured',
        'affiliate_code',
        'manager_id',
        'is_admin',
        'is_manager',
        'taxa_entrada',
        'taxa_entrada_fixo',
        'taxa_saida',
        'taxa_saida_fixo',
        'taxa_extorno',
        'split_fixed',
        'split_variable',
        'bloquear_saque',
        'is_blocked',
        'preferred_gateway',
        'cep',
        'address',
        'address_number',
        'address_complement',
        'neighborhood',
        'city',
        'state',
        'zip_code',
        'street',
        'number',
        'person_type',
        'doc_front',
        'doc_back',
        'selfie_with_doc',
        'cnpj_card',
        'facial_biometrics',
        'kyc_step',
        'rejection_reason',
        'withdrawal_auto',
        'first_withdrawal_completed',
        'google2fa_secret',
        'google2fa_enabled',
        'withdrawal_mode',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'pin',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'pin' => 'hashed',
            'birth_date' => 'date',
            'is_admin' => 'boolean',
            'is_manager' => 'boolean',
            'is_approved' => 'boolean',
            'is_blocked' => 'boolean',
            'pin_configured' => 'boolean',
            'taxa_entrada' => 'decimal:2',
            'taxa_entrada_fixo' => 'decimal:2',
            'taxa_saida' => 'decimal:2',
            'taxa_saida_fixo' => 'decimal:2',
            'taxa_extorno' => 'decimal:2',
            'split_fixed' => 'decimal:2',
            'split_variable' => 'decimal:2',
            'monthly_billing' => 'decimal:2',
            'bloquear_saque' => 'boolean',
            'first_withdrawal_completed' => 'boolean',
        ];
    }

    /**
     * Relacionamento: User tem uma Wallet
     */
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    /**
     * Relacionamento: User tem muitas Transactions
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Relacionamento: User tem muitas Withdrawals
     */
    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    /**
     * Relacionamento: User pode ter um Manager (self-referencing)
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Relacionamento: User pode ter muitos subordinados
     */
    public function subordinates()
    {
        return $this->hasMany(User::class, 'manager_id');
    }

    /**
     * Relacionamento: User tem muitas ApiTokens
     */
    public function apiTokens()
    {
        return $this->hasMany(ApiToken::class);
    }

    /**
     * Relacionamento: User tem muitos tickets de suporte
     */
    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    /**
     * Relacionamento: User tem muitos PaymentSplits (splits que ele configura)
     */
    public function paymentSplits()
    {
        return $this->hasMany(PaymentSplit::class);
    }

    /**
     * Relacionamento: User recebe muitos splits (como recipient)
     */
    public function receivedSplits()
    {
        return $this->hasMany(PaymentSplit::class, 'recipient_user_id');
    }

    /**
     * Relacionamento: User recebe muitos TransactionSplits
     */
    public function transactionSplits()
    {
        return $this->hasMany(TransactionSplit::class, 'recipient_user_id');
    }

    /**
     * Obtém a taxa de entrada PIX
     * Prioriza a taxa configurada no usuário, se não houver, usa a do painel
     */
    public function getCashinPixPercentual(): float
    {
        if (!is_null($this->taxa_entrada)) {
            return floatval($this->taxa_entrada);
        }
        return floatval(Setting::get('cashin_pix_percentual', Setting::get('cashin_percentual', '3.00')));
    }

    /**
     * Obtém a taxa fixa de entrada PIX
     * Prioriza a taxa configurada no usuário, se não houver, usa a do painel
     */
    public function getCashinPixFixo(): float
    {
        if (!is_null($this->taxa_entrada_fixo)) {
            return floatval($this->taxa_entrada_fixo);
        }
        return floatval(Setting::get('cashin_pix_fixo', Setting::get('cashin_fixo', '1.00')));
    }

    /**
     * Obtém a taxa de entrada Cartão do painel
     * Prioriza a configuração da página de gateways, senão usa a geral
     */
    public function getCashinCardPercentual(): float
    {
        $fee = Setting::get('credit_card_transaction_fee_percent');
        if ($fee !== null && $fee !== '') {
            return floatval($fee);
        }
        return floatval(Setting::get('cashin_card_percentual', '6.00'));
    }

    /**
     * Obtém a taxa fixa de entrada Cartão do painel
     * Prioriza a configuração da página de gateways, senão usa a geral
     */
    public function getCashinCardFixo(): float
    {
        $fee = Setting::get('credit_card_transaction_fee_fixed');
        if ($fee !== null && $fee !== '') {
            return floatval($fee);
        }
        return floatval(Setting::get('cashin_card_fixo', '1.00'));
    }

    /**
     * Obtém a taxa de saída PIX
     * Prioriza a taxa configurada no usuário, se não houver, usa a do painel
     */
    public function getCashoutPixPercentual(): float
    {
        if (!is_null($this->taxa_saida)) {
            return floatval($this->taxa_saida);
        }
        return floatval(Setting::get('cashout_pix_percentual', Setting::get('cashout_percentual', '2.00')));
    }

    /**
     * Obtém a taxa fixa de saída PIX
     * Prioriza a taxa configurada no usuário, se não houver, usa a do painel
     */
    public function getCashoutPixFixo(): float
    {
        if (!is_null($this->taxa_saida_fixo)) {
            return floatval($this->taxa_saida_fixo);
        }
        return floatval(Setting::get('cashout_pix_fixo', Setting::get('cashout_fixo', '1.00')));
    }

    /**
     * Sincroniza as taxas do usuário com as taxas do painel
     */
    public function syncFeesFromPanel(): void
    {
        $this->taxa_entrada = $this->getCashinPixPercentual();
        $this->taxa_entrada_fixo = $this->getCashinPixFixo();
        $this->taxa_saida = $this->getCashoutPixPercentual();
        $this->taxa_saida_fixo = $this->getCashoutPixFixo();
        $this->save();
    }
}

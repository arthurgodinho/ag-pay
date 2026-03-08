<?php

namespace App\Services\Gateways;

interface PaymentGatewayInterface
{
    /**
     * Cria um pagamento PIX
     *
     * @param float $amount Valor do pagamento
     * @param array $payerData Dados do pagador (nome, email, cpf, etc)
     * @return array Resposta do gateway com status e qr_code
     */
    public function createPix(float $amount, array $payerData): array;

    /**
     * Cria um pagamento com cartão de crédito
     *
     * @param float $amount Valor do pagamento
     * @param array $cardData Dados do cartão (número, cvv, validade, etc)
     * @param array $payerData Dados do pagador (nome, email, cpf, etc)
     * @return array Resposta do gateway com status e informações do pagamento
     */
    public function createCreditCard(float $amount, array $cardData, array $payerData): array;

    /**
     * Cria um pagamento via Boleto
     *
     * @param float $amount Valor do pagamento
     * @param array $payerData Dados do pagador (nome, email, cpf, etc)
     * @return array Resposta do gateway com status e código de barras
     */
    public function createBoleto(float $amount, array $payerData): array;

    /**
     * Consulta o status de uma transação no gateway
     * 
     * @param string $transactionId ID da transação no gateway
     * @return array Dados da transação com status atualizado
     */
    public function consultTransaction(string $transactionId): array;
}



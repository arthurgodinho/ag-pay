<?php

namespace App\Helpers;

class DocumentHelper
{
    /**
     * Verifica se o documento é CPF (11 dígitos) ou CNPJ (14 dígitos)
     * 
     * @param string $document
     * @return string 'cpf' ou 'cnpj'
     */
    public static function getDocumentType(string $document): string
    {
        // Remove formatação (pontos, traços, barras, espaços)
        $cleanDocument = preg_replace('/[^0-9]/', '', $document);
        
        // CPF tem 11 dígitos, CNPJ tem 14 dígitos
        if (strlen($cleanDocument) === 11) {
            return 'cpf';
        } elseif (strlen($cleanDocument) === 14) {
            return 'cnpj';
        }
        
        // Por padrão, assume CPF se não conseguir identificar
        return 'cpf';
    }

    /**
     * Verifica se é pessoa física
     */
    public static function isPessoaFisica(string $document): bool
    {
        return self::getDocumentType($document) === 'cpf';
    }

    /**
     * Verifica se é pessoa jurídica
     */
    public static function isPessoaJuridica(string $document): bool
    {
        return self::getDocumentType($document) === 'cnpj';
    }
}










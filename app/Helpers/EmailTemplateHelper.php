<?php

namespace App\Helpers;

use App\Helpers\LogoHelper;

class EmailTemplateHelper
{
    /**
     * Gera o template base HTML para emails com logo e design moderno
     *
     * @param string $content Conteúdo principal do email
     * @param string $primaryColor Cor primária (padrão: #00B2FF)
     * @param string $secondaryColor Cor secundária (padrão: #00D9AC)
     * @return string
     */
    public static function getBaseTemplate(string $content, string $primaryColor = '#00B2FF', string $secondaryColor = '#00D9AC'): string
    {
        $logoUrl = LogoHelper::getLogoUrl();
        $appName = LogoHelper::getSystemName();
        $appUrl = config('app.url');
        
        // Logo HTML
        $logoHtml = '';
        if ($logoUrl) {
            $logoHtml = '<img src="' . e($logoUrl) . '" alt="' . e($appName) . '" style="max-width: 200px; height: auto; margin: 0 auto 30px; display: block;">';
        } else {
            $logoHtml = '<h1 style="color: ' . $primaryColor . '; font-size: 28px; font-weight: bold; text-align: center; margin: 0 0 30px;">' . e($appName) . '</h1>';
        }
        
        return '<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{subject}}</title>
    <!--[if mso]>
    <style type="text/css">
        body, table, td {font-family: Arial, sans-serif !important;}
    </style>
    <![endif]-->
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, \'Helvetica Neue\', Arial, sans-serif; background-color: #f5f7fa; line-height: 1.6;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f5f7fa;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); overflow: hidden;">
                    <!-- Header com Logo -->
                    <tr>
                        <td style="background: linear-gradient(135deg, ' . $primaryColor . ' 0%, ' . $secondaryColor . ' 100%); padding: 40px 30px; text-align: center;">
                            ' . $logoHtml . '
                        </td>
                    </tr>
                    
                    <!-- Conteúdo Principal -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            ' . $content . '
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 30px; text-align: center; border-top: 1px solid #e9ecef;">
                            <p style="margin: 0 0 10px; color: #6c757d; font-size: 14px;">
                                <strong style="color: #212529;">' . e($appName) . '</strong>
                            </p>
                            <p style="margin: 0 0 10px; color: #6c757d; font-size: 12px;">
                                Este é um email automático, por favor não responda.
                            </p>
                            <p style="margin: 0; color: #6c757d; font-size: 12px;">
                                <a href="' . e($appUrl) . '" style="color: ' . $primaryColor . '; text-decoration: none;">' . e($appUrl) . '</a>
                            </p>
                            <p style="margin: 15px 0 0; color: #adb5bd; font-size: 11px;">
                                © ' . date('Y') . ' ' . e($appName) . '. Todos os direitos reservados.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
    }
    
    /**
     * Gera botão CTA moderno
     */
    public static function getButton(string $text, string $url, string $color = '#00B2FF'): string
    {
        return '<table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin: 30px auto;">
            <tr>
                <td align="center" style="border-radius: 8px; background-color: ' . $color . ';">
                    <a href="' . e($url) . '" style="display: inline-block; padding: 14px 32px; color: #ffffff; text-decoration: none; font-weight: 600; font-size: 16px; border-radius: 8px; background-color: ' . $color . ';">' . e($text) . '</a>
                </td>
            </tr>
        </table>';
    }
    
    /**
     * Gera card de informação
     */
    public static function getInfoCard(string $content, string $borderColor = '#00B2FF'): string
    {
        return '<div style="background-color: #f8f9fa; border-left: 4px solid ' . $borderColor . '; padding: 20px; border-radius: 6px; margin: 20px 0;">
            ' . $content . '
        </div>';
    }
}


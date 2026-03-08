<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailCampaign extends Model
{
    protected $fillable = [
        'name',
        'subject',
        'body_html',
        'body_text',
        'status',
        'scheduled_at',
        'sent_at',
        'total_recipients',
        'sent_count',
        'failed_count',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'total_recipients' => 'integer',
        'sent_count' => 'integer',
        'failed_count' => 'integer',
    ];

    /**
     * Relacionamento com logs de email
     */
    public function emailLogs(): HasMany
    {
        return $this->hasMany(EmailLog::class, 'campaign_id');
    }

    /**
     * Renderiza o template com variáveis
     */
    public function renderForUser($user, array $additionalVariables = [])
    {
        $html = $this->body_html;
        $text = $this->body_text ?? strip_tags($html);
        $subject = $this->subject;

        $variables = array_merge([
            'user_name' => $user->name ?? 'Usuário',
            'user_email' => $user->email,
            'app_name' => config('app.name'),
            'app_url' => config('app.url'),
        ], $additionalVariables);

        foreach ($variables as $key => $value) {
            $html = str_replace('{{' . $key . '}}', $value, $html);
            $text = str_replace('{{' . $key . '}}', $value, $text);
            $subject = str_replace('{{' . $key . '}}', $value, $subject);
        }

        return [
            'subject' => $subject,
            'html' => $html,
            'text' => $text,
        ];
    }
}


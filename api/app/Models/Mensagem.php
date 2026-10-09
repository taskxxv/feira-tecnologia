<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mensagem extends Model
{
    protected $table = 'mensagens';

    protected $fillable = [
        'conversa_id',
        'remetente_id',
        'conteudo',
        'post_id',
        'tipo',
        'lida_em',
    ];

    protected function casts(): array
    {
        return [
            'lida_em' => 'datetime',
        ];
    }

    public function conversa(): BelongsTo
    {
        return $this->belongsTo(Conversa::class, 'conversa_id');
    }

    public function remetente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'remetente_id');
    }

    public function publicacao(): BelongsTo
    {
        // Publicações pendentes ou reprovadas não são exibidas na DM.
        return $this->belongsTo(Post::class, 'post_id')
            ->where('status', Post::STATUS_APPROVED);
    }
}
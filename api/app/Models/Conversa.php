<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conversa extends Model
{
    protected $table = 'conversas';

    protected $fillable = [
        'usuario_1_id',
        'usuario_2_id',
    ];

    public function scopeDoUsuario(Builder $query, int $usuarioId): Builder
    {
        return $query->where(function (Builder $query) use ($usuarioId) {
            $query->where('usuario_1_id', $usuarioId)
                ->orWhere('usuario_2_id', $usuarioId);
        });
    }

    public function usuario1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_1_id');
    }

    public function usuario2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_2_id');
    }

    public function mensagens(): HasMany
    {
        return $this->hasMany(Mensagem::class, 'conversa_id');
    }

    public function ultimaMensagem(): HasOne
    {
        return $this->hasOne(Mensagem::class, 'conversa_id')
            ->latestOfMany();
    }
}
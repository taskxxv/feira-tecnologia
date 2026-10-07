<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Post extends Model
{
    use HasFactory;

    protected $table = 'publicacoes';

    public const STATUS_APPROVED = 'aprovado';
    public const STATUS_PENDING = 'pendente';
    public const STATUS_REJECTED = 'reprovado';

    protected $fillable = ['user_id', 'subject_id', 'title', 'content', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function embedding(): HasOne
    {
        return $this->hasOne(PostEmbedding::class);
    }
}

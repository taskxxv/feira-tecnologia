<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostEmbedding extends Model
{
    use HasFactory;

    protected $table = 'vetores_publicacoes';

    protected $fillable = ['post_id', 'embedding'];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}

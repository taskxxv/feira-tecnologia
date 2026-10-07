<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnonymousReport extends Model
{
    use HasFactory;

    protected $table = 'denuncias_anonimas';

    public const STATUS_NEW = 'nova';
    public const STATUS_READ = 'lida';
    public const STATUS_RESOLVED = 'resolvida';

    protected $fillable = ['original_content', 'ai_analysis', 'status'];

    protected function casts(): array
    {
        return [
            'ai_analysis' => 'array',
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NewsBlock extends Model
{
    use HasFactory;
    protected $table = 'news_blocks';
    protected $fillable = [
        'news_id',
        'content',
        'position',
    ];


    public function news(){
        return $this->belongsTo(News::class);
    }

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'position' => 'integer',
        ];
    }
}

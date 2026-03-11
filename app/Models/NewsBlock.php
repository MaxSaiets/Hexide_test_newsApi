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
        'type',
        'text_content',
        'image_path',
        'position',
    ];


    public function news(){
        return $this->belongsTo(News::class);
    }

    protected function casts(): array
    {
        return [
            'position' => 'integer',
        ];
    }
}

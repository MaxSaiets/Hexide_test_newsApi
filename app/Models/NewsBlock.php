<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\NewsBlockType;

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


    protected function imagePathUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->image_path ? Storage::url($this->image_path) : null,
        );
    }

    public function news(){
        return $this->belongsTo(News::class);
    }

    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'type' => NewsBlockType::class,
        ];
    }
}

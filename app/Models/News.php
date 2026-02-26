<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class News extends Model
{
    use HasFactory;
    protected $table = 'news';
    protected $fillable = [
        'title',
        'short_description',
        'image',
        'is_published',
        'published_at',
        'user_id',
        'slug',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function blocks()
    {
        return $this->hasMany(NewsBlock::class);
    }

    protected static function booted(): void
    {
        static::creating(function (News $news) {
            if (!$news->slug) {
                $news->slug = Str::slug($news->title) . '-' . Str::random(5);
            }

            if ($news->is_published && !$news->published_at) {
                $news->published_at = Carbon::now();
            }
        });
        static::updating(function (News $news) {

            if ($news->isDirty('is_published')) {
                if ($news->is_published == true) {
                    $news->published_at = Carbon::now();
                } else {
                    $news->published_at = null;
                }
            }
        });

    }

    public function scopeSearch($query, $search){
        if (!$search) {
            return $query;
        }
        return $query->where(function ($item) use ($search) {
            $item->where('title', 'like', '%' . $search . '%')
                ->orWhere('short_description', 'like', '%' . $search . '%');
        });
    }
}

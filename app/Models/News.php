<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'category_id',
        'user_id'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($news) {
            $news->slug = Str::slug($news->title);
        });
    }

    public function categorie()
    {
        return $this->belongsTo(Category::class, 'category_id'); // 'category_id' é o nome da chave estrangeira
    }
}

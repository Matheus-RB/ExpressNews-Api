<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class News extends Model
{
  use HasFactory;

  protected $fillable = [
    'category_id',
    'content',
    'introductory_paragraph',
    'image_description',
    'user_id',
    'title',
    'main_image'
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
    return $this->belongsTo(Category::class, 'category_id');
  }

  public function user()
  {
    return $this->belongsTo(User::class, 'user_id');
  }
}

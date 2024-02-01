<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcomment extends Model
{
    use HasFactory;

    protected $fillable = [
        'comment_id',
        'user_id',
        'content'
    ];

    public function likes()
    {
        return $this->hasMany(SubcommentLike::class);
    }
}

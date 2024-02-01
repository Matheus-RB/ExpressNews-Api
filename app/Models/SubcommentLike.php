<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubcommentLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'subcomment_id',
        'user_id'
    ];

    public function subcomment()
    {
        return $this->belongsTo(Subcomment::class);
    }
}

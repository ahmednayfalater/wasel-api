<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'target_type',
        'target_id',
        'rate',
        'comment',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

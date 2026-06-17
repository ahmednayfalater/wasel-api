<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'target_type',
        'target_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

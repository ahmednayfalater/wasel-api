<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Generator extends Model
{
    protected $fillable = [
        'provider_id',
        'type',
        'status',
        'gps',
        'powerKW',
        'price_KW',
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}

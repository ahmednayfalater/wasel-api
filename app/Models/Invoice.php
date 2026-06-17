<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'subscription_id',
        'previous_reading',
        'current_reading',
        'amount',
        'release_date',
        'due_date',
        'status',
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}

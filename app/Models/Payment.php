<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id',
        'amount',
        'receipt_image',
        'invoice_review',
        'pay_date',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}

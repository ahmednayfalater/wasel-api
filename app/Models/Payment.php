<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id',
        'amount',
        'receipt_image',
        'invoice_review',
        'pay_date',
    ];

    protected function receiptImage(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? Storage::url($value) : null,
        );
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}

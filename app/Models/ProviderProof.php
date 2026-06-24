<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProviderProof extends Model
{
    protected $fillable = ['provider_id', 'image_path'];

    protected function imagePath(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? Storage::url($value) : null,
        );
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
}

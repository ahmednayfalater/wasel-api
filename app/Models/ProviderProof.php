<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderProof extends Model
{
    protected $fillable = ['provider_id', 'image_path'];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
}

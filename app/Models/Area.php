<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $fillable = ['name', 'coordinates'];

    public function providers()
    {
        return $this->belongsToMany(Provider::class, 'provider_area');
    }
}

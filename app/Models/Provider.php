<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'terms_subscr',
        'accept_subscript',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function proofs()
    {
        return $this->hasMany(ProviderProof::class);
    }

    public function generators()
    {
        return $this->hasMany(Generator::class);
    }

    public function areas()
    {
        return $this->belongsToMany(Area::class, 'provider_area');
    }
}

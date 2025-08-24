<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    protected $casts = [
        'status' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
        'exchange_rate' => 'decimal:6',
        'country_tax' => 'decimal:1'
    ];

    protected $guarded = [];
    public function cities()
    {
        return $this->hasMany(City::class);
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }
}

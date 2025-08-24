<?php

namespace App\Models;

use App\Models\Scopes\LatestScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactUs extends Model
{
    protected $table = 'contact_us';
    protected $guarded = [];
    use HasFactory;
    protected static function booted()
    {
        static::addGlobalScope(new LatestScope);  // Apply the LatestScope
    }
}

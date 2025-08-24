<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Participant extends Pivot
{
    use HasFactory;
    protected $table = 'participants';

    public $incrementing = true;

    /**
     * Get the user participating in the chat.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the chat that the user is participating in.
     */
    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }
}
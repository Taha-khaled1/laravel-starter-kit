<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'last_message_id', 'user_id', 'type', 'product_id'];

    public function messages()
    {
        return $this->hasMany(Message::class, 'chat_id', 'id')->latest();
    }
    public function lastMessage()

    {
        return $this->belongsToMany(Message::class, 'last_message_id', 'id')
            ->withDefault();
    }
    public function lastMessageeWithUser()
    {
        return $this->hasOne(Message::class, 'id', 'last_message_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the participants of the chat.
     */
    public function participants()
    {
        return $this->belongsToMany(User::class, 'participants')->withPivot(["role"]);
    }
}

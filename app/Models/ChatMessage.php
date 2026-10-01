<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = ['chat_conversation_id', 'sender', 'message', 'knowledge_base_id', 'rating'];

    public function conversation()
    {
        return $this->belongsTo(ChatConversation::class, 'chat_conversation_id');
    }
}
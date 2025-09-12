<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'account_id', 'conversation_id', 'sender_id', 'sender_type', 'message_type', 'content', 'content_type', 'status', 'private', 'created_at', 'updated_at', 'payload_exist'];
    public $timestamps = false;

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function payloads()
    {
        return $this->hasMany(MessagePayload::class);
    }

    public function webhookEvents()
    {
        return $this->hasMany(WebhookEvent::class, 'message_id');
    }
}

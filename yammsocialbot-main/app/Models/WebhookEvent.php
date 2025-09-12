<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookEvent extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'event_type', 'raw_payload', 'received_at', 'message_id'];
    public $timestamps = false;

    public function message()
    {
        return $this->belongsTo(Message::class, 'message_id');
    }
}



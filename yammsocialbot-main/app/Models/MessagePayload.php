<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessagePayload extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'message_id', 'title', 'payload', 'type', 'image_url', 'footer'];
    public $timestamps = false;

    public function message()
    {
        return $this->belongsTo(Message::class);
    }
}
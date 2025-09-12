<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversationMetric extends Model
{
    protected $primaryKey = 'conversation_id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['conversation_id', 'first_response_time', 'total_messages', 'average_response_time'];
    public $timestamps = false;

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}
 
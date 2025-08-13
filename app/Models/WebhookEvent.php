<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookEvent extends Model
{
    protected $table = 'webhook_events';
    public $timestamps = false; // because 'received_at' is manually set

    protected $fillable = [
        'id',
        'event_type',
        'raw_payload',
        'received_at',
        'message_id'
    ];

    protected $casts = [
        'raw_payload' => 'array', // Laravel will handle jsonb
    ];
}

<?php

class Conversation extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'account_id', 'contact_id', 'assignee_id', 'status', 'channel', 'labels', 'created_at', 'updated_at'];
    public $timestamps = false;

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function contact()
    {
        return $this->belongsTo(User::class, 'contact_id');
    }

    public function assignee()
    {
        return $this->belongsTo(Assignee::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function metrics()
    {
        return $this->hasOne(ConversationMetric::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'account_id', 'name', 'is_business', 'phone_number', 'email', 'created_at', 'yamm_customer_id'];
    public $timestamps = false;

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class, 'contact_id');
    }
}

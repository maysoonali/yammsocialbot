<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignee extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'name', 'availability_stat', 'team'];
    public $timestamps = false;

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }
}

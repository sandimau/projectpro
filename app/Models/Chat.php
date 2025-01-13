<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    public $table = 'chats';

    protected $guarded = [];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

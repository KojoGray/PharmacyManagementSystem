<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class chatmessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'senderId',
        'receiverId',
        'messageBody',
        'role'
    ];
}

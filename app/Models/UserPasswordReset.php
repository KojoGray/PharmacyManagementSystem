<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPasswordReset extends Model
{
    protected $fillable=[
        'user_id',
        'code'
    ];
    use HasFactory;


}

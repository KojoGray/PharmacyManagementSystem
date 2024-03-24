<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class reminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'reminderMessage',
        'dosageId',
        'customerId'
    ];
}

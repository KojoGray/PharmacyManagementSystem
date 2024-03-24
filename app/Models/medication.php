<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class medication extends Model
{
    use HasFactory;

    protected $fillable = [
        'totalQuantity',
    'description',
    'medicineName',
    'medicinePrice', 
    'expiryDate' 
    ];
}

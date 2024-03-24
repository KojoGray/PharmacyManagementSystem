<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
   protected $fillable = [
    'customer_id',
    'medication_id',
    'sales_id',
    'unit_price',
    'unit_quantity'
   ];
}

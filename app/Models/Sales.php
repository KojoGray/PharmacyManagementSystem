<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    use HasFactory;
   protected   $fillable = [
                     'pharmacist_id',
                    'customer_id',
                    'total_quantity',
                    'total_price',
                    'status',
                    'amount_paid'
                 
     ];
    
}

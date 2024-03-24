<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dosages extends Model
{
    use HasFactory;

    protected $fillable = [
        'medication_id' ,
        'AgeFrom',
        'AgeTo',
        'ageCategory', 
        'dosageStrength',
        'dosageInstruction',
       
    ];
}

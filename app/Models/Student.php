<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'rfid',
        'alias',
        'first_name',
        'last_name',
        'middle_name',
        'current_points'
    ];
}

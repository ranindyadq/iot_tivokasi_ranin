<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = [
        'suhu',
        'kelembapan',
        'gas',
        'status',
    ];
}

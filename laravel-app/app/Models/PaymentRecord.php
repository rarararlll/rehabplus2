<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentRecord extends Model
{
    use HasFactory;

    protected $table = 'payment_records';

    protected $fillable = [
        'patient',
        'session',
        'fee',
        'status',
        'date',
    ];

    protected $casts = [
        'fee' => 'decimal:2',
        'date' => 'date',
    ];
}

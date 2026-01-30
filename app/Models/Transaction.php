<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'time',
        'rate',
        'gold_amount',
        'tax_status',
        'total_rupiah'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'date' => 'date',
        'time' => 'string',
        'rate' => 'integer',
        'gold_amount' => 'integer',
        'tax_status' => 'boolean',
        'total_rupiah' => 'double'
    ];
}

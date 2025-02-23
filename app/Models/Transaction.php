<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_provider',
        'status',
        'response_data'
    ];

    protected $casts = [
        'response_data' => 'array'
    ];

    
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

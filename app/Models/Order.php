<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Scopes\UserOrdersScope;


class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'status'
    ];

    protected static function booted()
    {
        static::addGlobalScope(new UserOrdersScope());
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

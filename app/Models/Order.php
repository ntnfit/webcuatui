<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_amount',
        'status',
        'payment_method',
        'shipping_address',
        'customer_name',
        'customer_email',
        'customer_phone',
    ];

    public function orderDetails()
    {
        return $this->hasMany(\App\Models\OrderDetail::class);
    }
}

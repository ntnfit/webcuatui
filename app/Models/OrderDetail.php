<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'line_total',
    ];

    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class);
    }

    public function order()
    {
        return $this->belongsTo(\App\Models\Order::class);
    }
}

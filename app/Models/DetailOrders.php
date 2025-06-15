<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailOrders extends Model
{
    protected $table = 'detail_orders';
    protected $fillable = ['order_id', 'product_id', 'quantity', 'price', 'total_sub'];
    protected $guarded = ['id'];

    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
}

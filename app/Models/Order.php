<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total',
        'shipping_address',
        'order_address',
        'order_email',
        'order_status',
    ];


    public function order_details()
    {
        return $this->hasMany(OrderDetail::class , 'order_id');
    }
}

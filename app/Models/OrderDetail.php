<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    /** @var string[] */
    protected $fillable = ['order_id', 'product_id', 'quantity', 'price'];
}

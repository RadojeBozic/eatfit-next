<?php
namespace App\Domain\Orders;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id','date','label','qty','unit_price_cents','total_cents'];
    public function order(){ return $this->belongsTo(Order::class); }
}

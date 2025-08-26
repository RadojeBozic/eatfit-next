<?php
namespace App\Domain\Orders;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Plans\{Plan,CalorieOption,Duration};
use App\Domain\Delivery\DeliveryZone;

class Order extends Model
{
    protected $fillable = [
        'user_id','status','payment_status','plan_id','calorie_option_id','duration_id','start_date',
        'customer_name','phone','email','address_line','city','postal_code','delivery_zone_id',
        'subtotal_cents','delivery_fee_cents','discount_cents','total_cents','currency','proforma_path'
    ];
    public function items(){ return $this->hasMany(OrderItem::class); }
    public function plan(){ return $this->belongsTo(Plan::class); }
    public function calorie(){ return $this->belongsTo(CalorieOption::class,'calorie_option_id'); }
    public function duration(){ return $this->belongsTo(Duration::class); }
    public function zone(){ return $this->belongsTo(DeliveryZone::class,'delivery_zone_id'); }
}

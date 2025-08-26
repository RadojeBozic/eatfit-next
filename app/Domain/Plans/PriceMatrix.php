<?php
namespace App\Domain\Plans;

use Illuminate\Database\Eloquent\Model;

class PriceMatrix extends Model
{
    protected $table = 'price_matrices'; // <= promeni sa 'price_matrix' na 'price_matrices'
    protected $fillable = ['plan_id','calorie_option_id','duration_id','price_cents','currency','active_from','active_to'];

    public function plan(){ return $this->belongsTo(Plan::class); }
    public function calorie(){ return $this->belongsTo(CalorieOption::class,'calorie_option_id'); }
    public function duration(){ return $this->belongsTo(Duration::class); }
}


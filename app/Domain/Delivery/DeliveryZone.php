<?php
namespace App\Domain\Delivery;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class DeliveryZone extends Model
{
    use HasTranslations;
    protected $fillable = ['name','fee_cents'];
    public $translatable = ['name'];
}

<?php
namespace App\Domain\Plans;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Plan extends Model
{
    use HasTranslations;
    protected $fillable = ['slug','name','description','is_active'];
    public $translatable = ['slug','name','description'];
    public function prices() { return $this->hasMany(PriceMatrix::class); }
}

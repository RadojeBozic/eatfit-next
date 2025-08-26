<?php
namespace App\Domain\Plans;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class CalorieOption extends Model
{
    use HasTranslations;
    protected $fillable = ['kcal','label'];
    public $translatable = ['label'];
}

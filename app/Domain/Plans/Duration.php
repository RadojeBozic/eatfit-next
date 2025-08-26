<?php
namespace App\Domain\Plans;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Duration extends Model
{
    use HasTranslations;
    protected $fillable = ['days','label'];
    public $translatable = ['label'];
}

<?php namespace App\Revago;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatalogCountry extends Model
{

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public $table = 'catalog_country';

    protected $fillable = ['name_ru', 'name_en', 'name_es'];

    /**
     * @return mixed
     */
    public function getName()
    {
        if ($this->{'name_' . \App::getLocale()}) {
            return $this->{'name_' . \App::getLocale()};
        }
        return $this->name_ru;
    }

}

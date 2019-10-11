<?php namespace App\Revago;

use Illuminate\Database\Eloquent\Model;

class CatalogItemsCalendar extends Model
{

    protected $table = 'catalog_items_calendar';

    protected $fillable = ['item_id', 'start', 'end'];

    public $timestamps = false;


}

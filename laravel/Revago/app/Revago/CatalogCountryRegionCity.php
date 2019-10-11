<?php namespace App\Revago;


class CatalogCountryRegionCity extends CatalogCountryRegion
{

    public $table = 'catalog_country_region_city';

    /**
     * @param int $limit
     * @return mixed
     */
    public function getCatalogItems($limit = 100)
    {
        $items = CatalogItems::where('city_id', $this->id)->paginate($limit);
        return $items;
    }

}

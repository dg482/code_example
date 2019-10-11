<?php namespace App\Revago;

use Illuminate\Database\Eloquent\Model;

class CatalogItemsCalendarSearch extends Model
{


    protected $table = 'catalog_items_calendar_search';


    /**
     * Возвращает занятые объекты по фильтру:
     * время начала бронирования, время окончания бронирования, ИД региона,ИД города
     * если объектов нет то все свободно
     *
     * @param array $filter
     * @return array
     */
    public static function getNotAllowedItems(array $filter)
    {
        $ids = [];
        if ($filter['date_end'] == 0 && $filter['date_end'] == 0) {
            return null;
        }
        /**
         * Ищем совпадение по дате начала бронирования
         */
        $items = CatalogItemsCalendarSearch::where(function ($query) use ($filter) {
            if ($filter['date_start'] && $filter['date_end']) {
                $query->whereRaw(\DB::raw('((CAST( "' . $filter['date_start'] . ' 00:00:00" AS DATE) BETWEEN start AND end
        OR CAST("' . $filter['date_end'] . ' 23:59:59" AS DATE) BETWEEN start AND end))'));
            }
            if ($filter['region_id']) {
                $query->where('region_id', $filter['region_id']);
            }
            if ($filter['city_id']) {
                $query->where('city_id', $filter['city_id']);
            }
        })->get();

        foreach ($items as $item) {
            array_push($ids, $item->item_id);
        }
        $ids = array_unique($ids);

        /**
         * Если есть совпадения по дате начала бронирования??? if (count($ids)) {}
         * Ишем по дате завершения бронирования
         */
        $items = CatalogItemsCalendarSearch::where(function ($query) use ($filter, $ids) {

            $query->whereRaw(\DB::raw('(start BETWEEN CAST("' . $filter['date_start'] . ' 00:00:00" AS DATE) AND CAST("' .
                $filter['date_end'] . ' 23:59:59" AS DATE)
        OR end BETWEEN CAST("' . $filter['date_start'] . ' 00:00:00" AS DATE) AND CAST("' .
                $filter['date_end'] . ' 23:59:59" AS DATE))'));

            if (count($ids)) {
                // $query->whereIn('id', $ids);
            }

            if ($filter['region_id']) {
                $query->where('region_id', $filter['region_id']);
            }
            if ($filter['city_id']) {
                $query->where('city_id', $filter['city_id']);
            }
        })->get();

        foreach ($items as $item) {
            array_push($ids, $item->item_id);
        }
        $ids = array_unique($ids);

        return (count($ids)) ? $ids : null;
    }
}

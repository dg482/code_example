<?php namespace App\Revago;

use Illuminate\Database\Eloquent\Model;

class SearchCatalog extends Model
{

    protected $table = 'catalog_search';

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

        /**
         * Если не указано окончание пребывания то прибавляем к началу 7 дней
         */
        if (!isset($filter['date_end']) || $filter['date_end'] == '') {
            $filter['date_end'] = (date('Y-m-d', time() + (7 * 24 * 60 * 60)));
        }
        \Input::merge([
            'date_from' => $filter['date_start'],
            'date_to' => $filter['date_end'],
        ]);
        /**
         * Ищем совпадение по дате начала бронирования
         */

        $items = SearchCatalog::where(function ($query) use ($filter) {
            $query->whereRaw(\DB::raw('((CAST( "' . $filter['date_start'] . ' 00:00:00" AS DATE) BETWEEN start AND end
        OR CAST("' . $filter['date_end'] . ' 23:59:59" AS DATE) BETWEEN start AND end))'));
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
        $items = SearchCatalog::where(function ($query) use ($filter, $ids) {
            $query->whereRaw(\DB::raw('(start BETWEEN CAST("' . $filter['date_start'] . ' 00:00:00" AS DATE) AND CAST("' .
                $filter['date_end'] . ' 23:59:59" AS DATE)
        OR end BETWEEN CAST("' . $filter['date_start'] . ' 00:00:00" AS DATE) AND CAST("' .
                $filter['date_end'] . ' 23:59:59" AS DATE))'));

            if (count($ids)) {
                $query->whereIn('id', $ids);
            }

            if (isset($filter['region_id']) && $filter['region_id'] > 0) {
                $query->where('region_id', $filter['region_id']);
            }
            if (isset($filter['city_id']) > 0) {
                $query->where('city_id', $filter['city_id']);
            }

        })->get();

        $ids = [];
        foreach ($items as $item) {
            array_push($ids, $item->item_id);
        }
        $ids = array_unique($ids);


        return (count($ids)) ? $ids : null;
    }
}

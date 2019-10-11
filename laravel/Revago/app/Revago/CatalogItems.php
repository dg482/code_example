<?php namespace App\Revago;


use  Intervention\Image\Facades\Image;

use App\Core\CatalogItems as CoreCatalogItems,
    App\Core\Catalog,
    App\Core\Content;

class CatalogItems extends CoreCatalogItems
{
    /**
     * @var CatalogItemPrices
     */
    public static $price;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function properties()
    {
        return $this->hasMany('App\Core\CatalogProperties', 'item_id', 'id');
    }


    /**
     * Получаем минимальную цену аренды,
     * учитываем период съема и текущую дату
     *
     * @return mixed
     */
    public function getMinPrice()
    {
        $date_from = \Input::get('date-from', date('Y-m-d'));
        $date_to = \Input::get('date-to', null);

        if ($date_to == null) {
            //ограничиваем выборку месяцем от текущей даты если не указан период
            //  $date_to = \Input::get('date_to', date('Y-m-d', (time() + 30 * 60 * 60)));
        } else {
            $date_to = date('Y-m-d 23:59:59', strtotime($date_to));
        }

        if ($date_from) {
            $date_from = date('Y-m-d 00:00:00', strtotime($date_from));
        }

        //минимальное кол-во дней
        $minimumStay = null;
        $filter = [
            'date_start' => $date_from,
            'date_end' => $date_to
        ];
//        if (self::$price == null) {
//
//        } else {
//            $prices = self::$price;
//        }
        $prices = CatalogItemPrices::where(function ($query) use ($minimumStay, $filter) {
            $query->where('item_id', $this->id);
            //с учетом периода размещения

            \Debugbar::info($filter);

            if ($filter['date_end'] && $filter['date_start']) {
                $query->whereRaw(\DB::raw("((CAST('{$filter['date_start']}' AS date) BETWEEN date_from AND date_to
OR CAST('{$filter['date_end']}' AS date) BETWEEN date_from AND date_to))"));
            } else if ($filter['date_start']) {
                $query->whereRaw(\DB::raw('`date_from` <= \'' . $filter['date_start'] . '\''));
            } else {
                $query->whereRaw(\DB::raw('`date_from` <= NOW() and `date_to` >= NOW()'));
            }
        })->first();
        self::$price = $prices;

        return ($prices) ? $prices->getPriceFormat() : '';
    }

    /**
     * @return mixed
     */
    public function getMinDay()
    {
        if ($date_from = \Input::get('date-from')) {

            $filter = [
                'date_start' => $date_from,
                'date_end' => \Input::get('date-to', null)
            ];
//            if (self::$price == null) {
//
//            } else {
//
//            }
            $price = CatalogItemPrices::where(function ($query) use ($filter) {
                $query->where('item_id', $this->id);
                //с учетом периода размещения
                if ($filter['date_end'] && $filter['date_start']) {
                    $query->whereRaw(\DB::raw('`date_from` <= \'' . $filter['date_start'] . '\' and `date_to` >= \'' . $filter['date_end'] . '\''));
                } else if ($filter['date_start']) {
                    $query->whereRaw(\DB::raw('`date_from` <= \'' . $filter['date_start'] . '\''));
                } else {
                    $query->whereRaw(\DB::raw('`date_from` <= NOW() and `date_to` >= NOW()'));
                }
            })->first();
            self::$price = $price;
            if ($price) {
                $price->minimum_stay = ($price->minimum_stay == 0) ? 1 : $price->minimum_stay;
                return $price->minimum_stay;
            }
        }


        $result = CatalogItemPrices::where('item_id', $this->id)->orderBy('minimum_stay')->first();
        return (isset($result->minimum_stay)) ? $result->minimum_stay : 0;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        $category = Catalog::where('id', $this->series_id)->first();
        if ($category) {
            $result = [\App::getLocale(), $category->alias, $this->alias];
            $save = ['date-from', 'date-to', 'guests'];
            $parameters = [];
            $inputData = \Input::all();
            foreach ($inputData as $key => $value) {
                if (in_array($key, $save)) {
                    $parameters[] = $key . '=' . $value;
                }
            }
            $url = url(implode('/', $result));
            if (count($parameters)) {
                $url .= '?' . implode('&', $parameters);
            }
            return $url;
        }
    }

    /**
     * Получение названия с учетом текущего языка
     *
     * @return mixed
     */
    public function getTitle()
    {
        if (is_string($this->attribs)) {
            $this->attribs = json_decode($this->attribs);
        }

        switch (\App::getLocale()) {
            case 'ru':
                return $this->title;
            case 'en':
                return $this->attribs->title_en;
            case 'es':
                return $this->attribs->title_es;
        }
    }


    /**
     * Получение региона вида: "Андалусия, Испания",
     * с учетом текущего языка
     *
     * @return string
     */
    public function getRegion()
    {
        $result = [];
        $url = ['direction'];
        $country_region = CatalogCountryRegion::where('id', $this->region_id)->first();
        if ($country_region) {
            array_push($url, $country_region->alias);

            if ($this->city_id) {
                $city = CatalogCountryRegionCity::where('id', $this->city_id)->first();
                array_push($url, $city->alias);
            }

            $url = implode('/', $url);
            $menuItem = Menu::where('alias', $url)->first();

            if ($menuItem) {
                array_push($result, '<a href="' . Menu::getUrl($menuItem) . '">' . $menuItem->getName() . '</a>');
            } else {
                array_push($result, $country_region->getName());
            }


            $country = CatalogCountry::where('id', $country_region->parent_id)->first();
            if ($country) {
                array_push($result, $country->getName());
            }
        }
        return implode(', ', $result);
    }


    /**
     * Возвращает описание позиции с учетом текущего языка
     *
     * @return mixed
     */
    public function getDescription($introtext = false)
    {
        if (is_string($this->attribs)) {
            $this->attribs = json_decode($this->attribs);
        }
        $_text = '';
        switch (\App::getLocale()) {
            case 'ru':
                $_text = ($introtext) ? $this->introtext : $this->fulltext;
                break;
            case 'en':
                $text = Content::getText($this->attribs->description_en);
                $_text = ($text->fulltext != '') ? $text->fulltext : $text->introtext;
                if ($introtext) {
                    $_text = ($text->introtext != '') ? $text->introtext : $text->introtext;
                }
                break;
            case 'es':
                $text = Content::getText($this->attribs->description_es);
                $_text = ($text->fulltext != '') ? $text->fulltext : $text->introtext;
                if ($introtext) {
                    $_text = ($text->introtext != '') ? $text->introtext : $text->introtext;
                }
                break;
        }
        if ($introtext === false) {
            preg_match('/(<p[^>]*>(.*)<\/p>){1}/isU', $_text, $matches);
            if (isset($matches[1])) {
                return $matches[1];
            }
        }
        return $_text;
    }

    /**
     * Возвращает дополнительное описание с учетом языка
     *
     * @return object
     */
    public function getDescriptionSecond()
    {
        if (is_string($this->attribs)) {
            $this->attribs = json_decode($this->attribs);
        }
        $text = '';
        if (isset($this->attribs->description_second)) {
            switch (\App::getLocale()) {
                case 'ru':
                    $text = $this->attribs->description_second;
                    break;
                case 'en':
                    $text = $this->attribs->description_second_en;
                    break;
                case 'es':
                    $text = $this->attribs->description_second_es;
                    break;
            }
        }
        if ($text == '') {
            $_text = '';
            switch (\App::getLocale()) {
                case 'ru':
                    $_text = $this->fulltext;
                    break;
                case 'en':
                    $text = Content::getText($this->attribs->description_en);
                    $_text = ($text->fulltext != '') ? $text->fulltext : $text->introtext;
                    break;
                case 'es':
                    $text = Content::getText($this->attribs->description_es);
                    $_text = ($text->fulltext != '') ? $text->fulltext : $text->introtext;
                    break;
            }
            $text = $_text;
        }
        preg_match('/(<p[^>]*>(.*)<\/p>){1}/isU', $text, $matches);
        if (isset($matches[1])) {
            $text = str_replace($matches[1], $matches[1] . '<div class="hid">', $text);
            $text .= '</div><button class="btn-link read-next">' . \Lang::get('app.Читать далее') . '</button>';
        }
        return $text;
    }

    /**
     * Возвращает 10 объектов из того же региона и города
     *
     * @return mixed
     */
    public function getOtherObject()
    {
        $items = self::where('region_id', $this->region_id)
            ->where('city_id', $this->city_id)
            ->whereNotIn('id', [$this->id])
            ->orderBy('rating')
            ->orderBy('title')
            ->limit(10)->get();

        return $items;
    }

    /**
     * Получение календаря для объекта
     *
     *
     * @return array
     */
    public function getAllowPeriods()
    {
        $result = [];
        $items = CatalogItemsCalendarSearch::where('item_id', $this->id)
            //->whereRaw(\DB::raw('start > NOW()'))
            ->get();
        foreach ($items as $k => $item) {
            if (strtotime($item->start) < time()) {
                $item->start = date('Y-m-d');
            }
            $result[] = [date('Y-m-d', strtotime($item->start)), date('Y-m-d', strtotime($item->end))];
        }
        return $result;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        $result = [];
        $url = ['direction'];
        $country_region = CatalogCountryRegion::where('id', $this->region_id)->first();
        if ($country_region) {
            array_push($url, $country_region->alias);

            if ($this->city_id) {
                $city = CatalogCountryRegionCity::where('id', $this->city_id)->first();
                array_push($url, $city->alias);
            }

            $url = implode('/', $url);
            $menuItem = Menu::where('alias', $url)->first();

            if ($menuItem) {
                array_push($result, $menuItem->getName());
            } else {
                array_push($result, $country_region->getName());
            }


            $country = CatalogCountry::where('id', $country_region->parent_id)->first();
            if ($country) {
                array_push($result, $country->getName());
            }
        }
        return implode(', ', $result);

    }

    /**
     * @return CatalogItemPrices
     */
    public function getPrice()
    {
        return self::$price;
    }
}

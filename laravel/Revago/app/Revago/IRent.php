<?php namespace App\Revago;

if (!$_SERVER['DOCUMENT_ROOT']) {
    $_SERVER['DOCUMENT_ROOT'] = dirname(dirname(dirname(__FILE__) . '../') . '../') . '/revago.sotape.com';
}
//core
use App\Core\Catalog,
    App\Core\CatalogProperties,
    App\Core\CatalogItemProperty;

use Illuminate\Database\Eloquent\Model,
    Illuminate\Support\Facades\Cache,
    Artisaninweb\SoapWrapper\Facades\SoapWrapper;

use App\Revago\BookingDetails;

/**
 * Class IRent
 * @package App
 */
class IRent extends Model
{

    const KEY = 'RVGO';

    const TYPE = 'irent';

    const TEST = false;
    /**
     * URL soap сервиса I Rent
     */
    const SOAP_SERVICE = 'http://booking.i-rent.net/booking_service/service.asmx?WSDL';

    // тестовый код - '9269CF76-50DB-4922-9607-B2EC77F0025C'
    const WEBSITE_TEST_CODE = '';

    const WEBSITE_CODE = '';

    const REFERRER_CODE = '';

    // тестовый код - '29B044E0-DE5B-4788-B73E-AF4E0379F8F6'
    const AGENT_CODE = '29B044E0-DE5B-4788-B73E-AF4E0379F8F6';

    /**
     * Массив ответов soap сервиса I Rent
     * @var array
     */
    private $soap_call_answer = [];

    const INFO_VILLA = 'http://data.i-rent.net/info_villa.aspx';
    const INFO_BOOK = 'http://data.i-rent.net/info_book.aspx';
    const INFO_LEGAL = 'http://data.i-rent.net/info_legal.aspx';

    /**
     * Импорт данных в локальную базу
     *
     * @throws \ErrorException
     */
    public function init()
    {
        $transport = new  \Curl\Curl();
        $transport->get(self::INFO_VILLA, [
            'website' => (self::TEST) ? self::WEBSITE_TEST_CODE : self::WEBSITE_CODE
        ]);
        if ($transport->http_status_code == 200) {
            $information = $transport->response;
        }
        if (is_string($information)) {
            $res = simplexml_load_string($information);
        } else {
            $res = $information;
        }

        $items = (int)$res->attributes()->{'items'};
        if ($items) {
            $_items = [];
            foreach ($res as $child) {
                $attr = $child->attributes();
                if ($id = (int)$attr->id) {
                    $_items[$id] = $this->getInfoVilla($id);
                    if ($_items[$id]) {
                        $data = (object)$_items[$id];
                        $this->msg('Обрабатывается id:' . $data->id);
                        $check = CatalogItems::where('irent_id', $data->id)->first();
                        // проверяем категорию размещения объекта
                        $series = Catalog::firstOrCreate([
                            'title' => $data->type,
                            'alias' => strtolower($data->type_en)
                        ]);
                        $this->msg('Категория размещения объекта #' . $data->id . ' - ' . $series->id . '(' . $series->title . ')');
                        // регион, если не существует создаем
                        $region = CatalogCountryRegion::where('name_en', $data->province)->first();

                        if (!$region) {
                            $this->msg('Регион размещения объекта #' . $data->id . ' не найден и будет создан.');
                            $region = CatalogCountryRegion::create([
                                'name_ru' => $data->province,
                                'name_en' => $data->province,
                                'name_es' => $data->province,
                                'parent_id' => 1
                            ]);
                        }
                        $this->msg('Регион размещения объекта #' . $data->id . ' - ' . $region->id . ' (' . $region->name_en . ')');
                        // город, если не существует создаем
                        $city = CatalogCountryRegionCity::where('name_en', $data->location)->first();

                        if (!$city) {
                            $this->msg('Город размещения объекта #' . $data->id . ' не найден и будет создан.');
                            $city = CatalogCountryRegionCity::create([
                                'name_ru' => $data->location,
                                'name_en' => $data->location,
                                'name_es' => $data->location,
                                'parent_id' => $region->id
                            ]);
                        }
                        $this->msg('Город размещения объекта #' . $data->id . ' - ' . $city->id . ' (' . $city->name_en . ')');
                        // копируем все картинки
                        $images = (array)$data->images;
                        if (!$series->alias) {
                            var_dump($data['id'], $data['type']);
                        }
                        $imageDir = $_SERVER['DOCUMENT_ROOT'] . '/upload/images/' . \Slug::make($series->alias) . '/';
                        $this->_checkDir($imageDir);
                        $imageDir .= $region->name_en . '/';
                        $this->_checkDir($imageDir);
                        $imageDir .= $data->name;
                        $this->_checkDir($imageDir);
                        if (!is_dir($imageDir)) {
                            mkdir($imageDir);
                            chmod($imageDir, 0755);
                        }
                        foreach ($images as $image) {
                            $name = explode('/', $image);
                            if (!file_exists($imageDir . '/' . end($name))) {
                                copy($image, $imageDir . '/' . end($name));
                            }
                            if (!isset($_items[$id]['image'])) {
                                $_items[$id]['image'] = '/upload/images/' . \Slug::make($series->alias) . '/' . $region->name_en . '/'
                                    . $data->name . '/' . end($name);
                            }
                        }
                        /**
                         * данные для вставки\обновления
                         */
                        $_data = [
                            'title' => $data->name,
                            'code' => $data->code,
                            'agent' => $data->agent_code,
                            'alias' => \Slug::make($data->name),
                            'attribs' => json_encode([
                                'image' => $_items[$id]['image'],
                                'title_en' => $data->name,
                                'title_es' => $data->name,
                                'description_second' => $data->description_second,
                                'description_en' => $data->description_en,
                                'description_second_en' => $data->description_second_en,
                                'description_es' => $data->description_es,
                                'description_second_es' => $data->description_second_es,
                                'address' => '',
                                'coordinates' => implode(',', $data->coordinates),
                                'agent_code' => $data->agent_code
                            ]),
                            'series_id' => $series->id,
                            'fulltext' => $data->introtext,
                            'introtext' => $data->introtext,
                            'image' => current($images),
                            'region_id' => $region->id,
                            'rating' => $data->rating,
                            'max_guest' => $data->people,
                            'city_id' => $city->id,
                            'irent_id' => $data->id,
                            'type' => 'irent'
                        ];
//                    $this->msg('По объекту #' . $data->id . ' подготовленны данные:');
//                    $this->msg(print_r($_data, true));
                        if (!$check) {
                            $this->msg('Объект #' . $data->id . ' не найден и будет создан.');
                            $check = CatalogItems::create($_data);
                        } else {
                            $this->msg('Объект #' . $data->id . '  найден #' . $check->id . ' и будет обновлен.');
                            CatalogItems::where('id', $check->id)->update($_data);
                        }

                        /**
                         * Обновление\добавление параметров вилл (свойства категорий каталога)
                         */
                        $pr = [];
                        foreach ($data->parameters as $key => $property) {
                            if (is_string($property)) {
                                $checkProp = CatalogProperties::where(function ($query) use ($key, $check) {
                                    $query->where('code', $key);
                                    $query->where('category_id', $check->series_id);
                                })->first();
                                if ($checkProp) {
                                    $this->msg('Объект #' . $data->id . '  найдено и будет обновлено свойство #' . $checkProp->id);
                                    switch ($checkProp->type) {
                                        case 'exist':
                                            $property = ($property == 'No') ? 'false' : 'true';
                                            break;
                                    }
                                    CatalogItemProperty::firstOrCreate([
                                        'item_id' => $check->id,
                                        'property_id' => $checkProp->id,
                                        'value' => $property
                                    ]);
                                } else {
                                    array_push($pr, $key);
                                }
                            }
                        }
                        /**
                         * Обновление\добавление цен на аренду с учетом периода времени
                         */


                        foreach ($data->prices['items'] as $price) {
                            $price['item_id'] = $check->id;
                            $_price = CatalogItemPrices::firstOrCreate($price);
                            $this->msg('Объект #' . $check->id . ' создана цена #' . $_price->id);
                            $this->msg(print_r($price, true));
                        }

                        /**
                         * Обновление\добавление календаря не свободных периодов
                         */
                        $calendar = $this->getCalendar($id);

                        // \DB::table('catalog_items_calendar')->truncate(); очистка всей таблицы не подходит так как там будут еще и интервалы по локальным объектам
                        if (count($calendar)) {
                            //CatalogItemsCalendar::where('item_id', $check->id)->delete();
                            $this->msg('Удаление старых данных для объекта #' . $check->id . ' из таблицы календаря.');
                            foreach ($calendar as $data) {
                                $data['item_id'] = $check->id;
                                $this->msg('Для объекта #' . $check->id . ' добавлен\обновлен интервал: ' . $data['start'] . ' - ' . $data['end']);
                                CatalogItemsCalendar::firstOrCreate($data);
                            }
                        }
                    }

                }
            }
        }
    }

    /**
     * Обновление\добавление календаря не свободных периодов
     *
     * @param $id int  i-rent id
     * @param $check_id int local id
     */
    public function createIRentObjectCalendar($id, $check_id)
    {
        $calendar = $this->getCalendar($id);

        if (count($calendar)) {
            //CatalogItemsCalendar::where('item_id', $check_id)->delete();
            foreach ($calendar as $data) {
                $data['item_id'] = $check_id;
                CatalogItemsCalendar::firstOrCreate($data);
            }
        }
    }

    /**
     * Проверка существования директории
     *
     * @param $imageDir
     */
    private function _checkDir($imageDir)
    {
        if (!is_dir($imageDir)) {
            mkdir($imageDir);
            chmod($imageDir, 0755);
            $this->msg('Создана директория: ' . $imageDir);
        }
    }

    /**
     * Возвращает массив дат не свободных для заселения в виде
     *
     * [ 'item_id'=>2987, 'start'=>2016-02-11, 'end'=>2016-06-11, ]
     *
     * @param $id
     * @return array
     * @throws \ErrorException
     */
    public function getCalendar($id)
    {
        $result = [];

        $transport = new \Curl\Curl();
        $information = null;
        $transport->get(self::INFO_BOOK, [
            'website' => (self::TEST) ? self::WEBSITE_TEST_CODE : self::WEBSITE_CODE,
            'id' => $id
        ]);

        if ($transport->http_status_code == 200) {
            $information = $transport->response;
        }
        if (is_string($information)) {
            $xml = simplexml_load_string($information);
        } else {
            $xml = $information;
        }

        if (isset($xml->{'accommodation'})) {
            foreach ($xml->{'accommodation'} as $item) {
                array_push($result, [
                    'item_id' => $id,
                    'start' => \Carbon\Carbon::createFromFormat('d/m/Y', (string)$item->{'begin'})->format('Y-m-d'),
                    'end' => \Carbon\Carbon::createFromFormat('d/m/Y', (string)$item->{'end'})->format('Y-m-d'),
                ]);
            }
        }
        return $result;
    }

    /**
     * @param $id
     * @return array
     * @throws \ErrorException
     */
    public function getInfoVilla($id)
    {
        $transport = new \Curl\Curl();
        $information = null;

//        if (!$information) {   }

        $transport->get(self::INFO_VILLA, [
            'website' => (self::TEST) ? self::WEBSITE_TEST_CODE : self::WEBSITE_CODE,
            'id' => $id
        ]);

        if ($transport->http_status_code == 200) {
            $information = $transport->response;
            //Cache::put('villa-' . $id, $information, 360);
        }
        if (is_string($information)) {
            $xml = simplexml_load_string($information);
        } else {
            $xml = $information;
        }

        $data = (array)$xml->information;
        $images = [];

        if ($xml->{'accommodation'}->{'photos'}) {
            foreach ($xml->{'accommodation'}->{'photos'}->children() as $item) {
                array_push($images, (string)$item);
            }
        }
        $description_ru = '';
        if ($xml->xpath('//descriptions/description[@language="ru"]')) {
            $description_ru = (string)$xml->xpath('//descriptions/description[@language="ru"]')[0];
        }
        $description_gb = '';
        if ($xml->xpath('//descriptions/description[@language="gb"]')) {
            $description_gb = (string)$xml->xpath('//descriptions/description[@language="gb"]')[0];
        }
        $description_es = '';
        if ($xml->xpath('//descriptions/description[@language="esp"]')) {
            $description_es = (string)$xml->xpath('//descriptions/description[@language="esp"]')[0];
        }


        preg_match('/(<p[^>]*>(.*)<\/p>){1}/isU', $description_ru, $matches);
        if (isset($matches[1])) {
            $description_ru = str_replace($matches[1], '', $description_ru);
        }

        preg_match('/(<p[^>]*>(.*)<\/p>){1}/isU', $description_gb, $matches_gb);
        if (isset($matches_gb[1])) {
            $description_gb = str_replace($matches_gb[1], '', $description_gb);
        }

        preg_match('/(<p[^>]*>(.*)<\/p>){1}/isU', $description_es, $matches_es);
        if (isset($matches_es[1])) {
            $description_es = str_replace($matches_es[1], '', $description_es);
        }

        $result = [
            'id' => (string)$xml->{'accommodation'}->attributes()->id,
            'code' => (string)$xml->{'accommodation'}->attributes()->code,
            'agent_code' => (string)$xml->{'accommodation'}->{'administrator'}->{'code'},
            'name' => (string)$xml->{'accommodation'}->{'name'},
            'country' => (string)$xml->xpath('//country/name[@language="ru"]')[0],
            'profile' => (string)$xml->xpath('//profile/name[@language="ru"]')[0],
            'type' => (string)$xml->xpath('//type/name[@language="ru"]')[0],
            'type_en' => (string)$xml->xpath('//type/name[@language="gb"]')[0],
            'turistic_area' => (string)$xml->{'accommodation'}->{'turistic_area'},
            'province' => (string)$xml->{'accommodation'}->{'province'},
            'location' => (string)$xml->{'accommodation'}->{'location'},
            'coordinates' => [
                'lat' =>
                    ($xml->{'accommodation'}->{'coordinates'}->{'latitude'}) ?
                        (string)$xml->{'accommodation'}->{'coordinates'}->{'latitude'}->attributes()->{'decimal'}[0] : null,
                'lon' =>
                    ((string)$xml->{'accommodation'}->{'coordinates'}->{'longitude'}) ?
                        (string)$xml->{'accommodation'}->{'coordinates'}->{'longitude'}->attributes()->{'decimal'}[0] : null,
            ],
            'people' => (string)$xml->{'accommodation'}->{'people'},
            'pets' => ((string)$xml->{'accommodation'}->{'pets'} == 'Yes') ? true : false,
            'active' => ((string)$xml->{'accommodation'}->{'active'} == 'Yes') ? true : false,
            'images' => $images,
            'rating' => (string)$xml->{'accommodation'}->{'evaluation'}->{'stars'},
            'view' => (string)$xml->{'accommodation'}->{'evaluation'}->{'view'},
            'privacy' => (string)$xml->{'accommodation'}->{'evaluation'}->{'privacy'},
            'interior' => (string)$xml->{'accommodation'}->{'evaluation'}->{'interior'},
            'exterior' => (string)$xml->{'accommodation'}->{'evaluation'}->{'exterior'},
            'parameters' => [
                strtolower((string)$xml->xpath('//type/name[@language="gb"]')[0]) => 'Yes',
                'kitchen' => (string)$xml->{'accommodation'}->{'evaluation'}->{'kitchen'},
                'bathroom' => (string)$xml->{'accommodation'}->{'evaluation'}->{'bathroom'},
                'pool' => (string)$xml->{'accommodation'}->{'outdoor'}->{'pool'}->{'pool_class'},
                'pool_toilet' => (string)$xml->{'accommodation'}->{'outdoor'}->{'pool'}->{'toilet'},
                'jacuzzi' => (string)$xml->{'accommodation'}->{'outdoor'}->{'pool'}->{'jacuzzi'},
                'tennis_court' => ((string)$xml->{'accommodation'}->{'outdoor'}->{'tennis_court'} == 'Yes') ? true : false,
                'parking' => [
                    'garage' => (string)$xml->{'accommodation'}->{'outdoor'}->{'parking'}->{'garage'},
                    'covered' => (string)$xml->{'accommodation'}->{'outdoor'}->{'parking'}->{'covered'},
                    'non_covered' => (string)$xml->{'accommodation'}->{'outdoor'}->{'parking'}->{'non_covered'},
                    'exterior' => (string)$xml->{'accommodation'}->{'outdoor'}->{'parking'}->{'exterior'},
                ],
                'central_heating' => (string)$xml->{'accommodation'}->{'indoor'}->{'central_heating'},
                'hot_water_supply' => (string)$xml->{'accommodation'}->{'indoor'}->{'hot_water_supply'},
                'portable_electric_radiators' => (string)$xml->{'accommodation'}->{'indoor'}->{'portable_electric_radiators'},
                'portable_gas_heaters' => (string)$xml->{'accommodation'}->{'indoor'}->{'portable_gas_heaters'},
                'telephone' => ((string)$xml->{'accommodation'}->{'indoor'}->{'telephone'} == 'Yes') ? true : false,
                'ipod_base' => (string)$xml->{'accommodation'}->{'indoor'}->{'ipod_base'},
                'internet' => (string)$xml->{'accommodation'}->{'indoor'}->{'internet'},
                'local_net' => (string)$xml->{'accommodation'}->{'indoor'}->{'local_net'},
                'computer' => (string)$xml->{'accommodation'}->{'indoor'}->{'computer'},
                'satellite_antenna' => (string)$xml->{'accommodation'}->{'indoor'}->{'satellite_antenna'},
                'cable_tv' => (string)$xml->{'accommodation'}->{'indoor'}->{'cable_tv'},
                'gym' => (string)$xml->{'accommodation'}->{'indoor'}->{'gym'},
                'entertainment_room' => (string)$xml->{'accommodation'}->{'indoor'}->{'entertainment_room'},
                'mini_bar' => (string)$xml->{'accommodation'}->{'indoor'}->{'mini_bar'},
                'billiard' => (string)$xml->{'accommodation'}->{'indoor'}->{'billiard'},
                'foosball' => (string)$xml->{'accommodation'}->{'indoor'}->{'foosball'},
                'game_computer' => (string)$xml->{'accommodation'}->{'indoor'}->{'game_computer'},
                'alarm_system' => (string)$xml->{'accommodation'}->{'indoor'}->{'alarm_system'},
                'safe' => (string)$xml->{'accommodation'}->{'indoor'}->{'safe'},
                'total_floors' => (string)$xml->{'accommodation'}->{'total_floors'},
                'total_rooms' => (string)$xml->{'accommodation'}->{'total_rooms'},
                'receptionist' => (string)$xml->{'accommodation'}->{'extra_services'}->{'receptionist'},
                'accommodation_resort' => (string)$xml->{'accommodation'}->{'extra_services'}->{'accommodation_resort'},
                'golf_resort' => (string)$xml->{'accommodation'}->{'extra_services'}->{'golf_resort'},
                'beach_resort' => (string)$xml->{'accommodation'}->{'extra_services'}->{'beach_resort'},
                'ski_resort' => (string)$xml->{'accommodation'}->{'extra_services'}->{'ski_resort'},
                'restaurant' => (string)$xml->{'accommodation'}->{'extra_services'}->{'restaurant'},
                'bar' => (string)$xml->{'accommodation'}->{'extra_services'}->{'bar'},
                'surveillance' => (string)$xml->{'accommodation'}->{'extra_services'}->{'surveillance'},
                'maid' => (string)$xml->{'accommodation'}->{'extra_services'}->{'maid'},
                'cook' => (string)$xml->{'accommodation'}->{'extra_services'}->{'cook'},
                'laundry' => (string)$xml->{'accommodation'}->{'extra_services'}->{'laundry'},
                'babysit' => (string)$xml->{'accommodation'}->{'extra_services'}->{'babysit'},
                'bedding' => (string)$xml->{'accommodation'}->{'extra_services'}->{'bedding'},
                'emergency_phone' => (string)$xml->{'accommodation'}->{'extra_services'}->{'emergency_phone'},
                'paddle_court' => (string)$xml->{'accommodation'}->{'extra_services'}->{'paddle_court'},
                'squash_court' => (string)$xml->{'accommodation'}->{'extra_services'}->{'squash_court'},
                'playground' => (string)$xml->{'accommodation'}->{'extra_services'}->{'playground'},
                'soccer_field' => (string)$xml->{'accommodation'}->{'extra_services'}->{'soccer_field'},
                'fitness_area' => (string)$xml->{'accommodation'}->{'extra_services'}->{'fitness_area'},
                'sauna' => (string)$xml->{'accommodation'}->{'extra_services'}->{'sauna'},
                'table_tennis' => (string)$xml->{'accommodation'}->{'extra_services'}->{'table_tennis'},
                'airport' => (string)$xml->{'accommodation'}->{'extra_services'}->{'airport'},
                'community_swimming_pool' => (string)$xml->{'accommodation'}->{'extra_services'}->{'community_swimming_pool'},
                'children_pool' => (string)$xml->{'accommodation'}->{'extra_services'}->{'children_pool'},
                'family_suitable' => (string)$xml->{'accommodation'}->{'extra_services'}->{'family_suitable'},
                'wheelchair_suitable' => (string)$xml->{'accommodation'}->{'extra_services'}->{'wheelchair_suitable'},
                'no_smoking' => (string)$xml->{'accommodation'}->{'extra_services'}->{'no_smoking'},
                'spa' => (string)$xml->{'accommodation'}->{'extra_services'}->{'spa'},
                'trekking' => (string)$xml->{'accommodation'}->{'extra_services'}->{'trekking'},
            ],
            'introtext' => (isset($matches[1])) ? $matches[1] : '',
            'fulltext' => $description_ru,
            'description' => (isset($matches[1])) ? $matches[1] : '',
            'description_second' => $description_ru,
            'description_en' => (isset($matches_gb[1])) ? $matches_gb[1] : '',
            'description_second_en' => $description_gb,
            'description_es' => (isset($matches_es[1])) ? $matches_es[1] : '',
            'description_second_es' => $description_es
        ];
        $prices = [
            'items' => [],
            'unit' => null,
        ];

        if ($xml->{'accommodation'}->{'prices'}->{'unit'}) {
            $prices = [
                'items' => [],
                'unit' => (string)$xml->{'accommodation'}->{'prices'}->{'unit'}->attributes(),
            ];
        }


        if ($xml->{'accommodation'}->{'prices'}) {
            $_attr = $xml->{'accommodation'}->{'prices'}->{'unit'}->attributes();
            $_period = ((int)$_attr['code']) ? (int)$_attr['code'] : 7;
            foreach ($xml->{'accommodation'}->{'prices'}->{'interval'} as $item) {
                $attr = $item->attributes();
                $this->msg('Цена: ' . round(((float)$item) / $_period, 2) . ' (' . ((float)$item) . '/' . $_period . ')');
                array_push($prices['items'], [
                    'date_from' => \Carbon\Carbon::createFromFormat('d/m/Y', (string)$attr->from)->format('Y-m-d'),
                    'date_to' => \Carbon\Carbon::createFromFormat('d/m/Y', (string)$attr->until)->format('Y-m-d'),
                    'minimum_stay' => (int)$attr->minimum_stay,
                    'price' => round((((float)$item) / $_period), 2)
                ]);
            }
        }
        $result['prices'] = $prices;

        return $result;
    }

    /**
     * Вывод сообщения в консоль
     *
     * @param $msg
     */
    private function msg($msg)
    {
        echo $msg . PHP_EOL;
    }


    /**
     * Бронирование объекта недвижимости в сервисе i-rent
     * В случае ошибок смотреть результаты работы soap в self::getSoapCallAnswer()
     *
     * @return bool
     */
    public function order()
    {
        $self = $this;
        $result = false;
        $process_stack = [];
        SoapWrapper::add(function ($service) {
            $service->name('i-rent')
                ->wsdl(self::SOAP_SERVICE)
                ->trace(true)
                ->cache(WSDL_CACHE_NONE);
        });
        $raw_data = \Input::all();

        if (self::TEST) {
            // test ID
            // $raw_data['item_id'] = 111;
        }

        $item = CatalogItems::where('id', $raw_data['item_id'])->first();
        $raw_data['item'] = $item;

        if (!$item) {
            return false;
        }

        $date_start = $raw_data['date_from'] . 'T00:00:00';
        $date_end = $raw_data['date_to'] . 'T00:00:00';
        $accommodation = $item->code;
        $agent = $item->agent;

        $data = (object)[
            'FullDetails' => false,
            'Booking' => [
                'Website' => (self::TEST) ? self::WEBSITE_TEST_CODE : self::WEBSITE_CODE,
                'Agent' => $agent,
                'Referrer' => self::REFERRER_CODE,
                'Accommodation' => $accommodation,
                'From' => $date_start,
                'Till' => $date_end,
                'People' => $raw_data['guest'],
                'Language' => $raw_data['lang'],
                'Payment_Method' => 'OFFLINE',
            ],
        ];
        /**
         * Работаем внутри soap сервиса i-rent
         */
        SoapWrapper::service('i-rent', function ($service)
        use (
            $data, $raw_data, $date_start, $date_end, $agent, $accommodation,
            $self, &$result, &$process_stack, $item
        ) {
            $ask = $service->call('Ask_Availability', [$data]);
            array_push($process_stack, $ask);
            if ($ask->Ask_AvailabilityResult->Availability == 'FREE_PERIOD') {
                $_data = (object)[
                    'Booking' => [
                        'Website' => (self::TEST) ? self::WEBSITE_TEST_CODE : self::WEBSITE_CODE,
                        'Agent' => $agent,
                        'Referrer' => self::REFERRER_CODE,
                        'Accommodation' => $accommodation,
                        'From' => $date_start,
                        'Till' => $date_end,
                        'People' => $raw_data['guest'],
                        'Language' => $raw_data['lang'],
                        'Payment_Method' => 'OFFLINE',
                        'Tenant' => (object)[
                            'Language' => $raw_data['lang'],
                            'Treatment' => 'MR',//or MRS
                            'Name' => $raw_data['name'],
                            'Surname' => $raw_data['surname'],
                            'Country' => '7',
                            'City' => $raw_data['city'],
                            'Address' => $raw_data['address'],
                            'PostalCode' => $raw_data['zip_code'],
                            'Telephone' => $raw_data['phone'],
                            'Birth' => '1981-10-09T00:00:00',
                        ]
                    ]
                ];
                $ask = $service->call('Make_Booking', [$_data]);
                array_push($process_stack, $ask);
                /**
                 * Опрос доступности выбранного периода
                 */
                if ($ask->Make_BookingResult->Availability == 'FREE_PERIOD') {
                    $__data = (object)[
                        'Occupation' => [
                            'Website' => (self::TEST) ? self::WEBSITE_TEST_CODE : self::WEBSITE_CODE,
                            'Agent' => $agent,
                            'Referrer' => self::REFERRER_CODE,
                            'Accommodation' => $accommodation,
                            'From' => $date_start,
                            'Till' => $date_end,
                            'People' => 3,
                            'Language' => 'RU',
                            'Number' => 1
                        ]
                    ];
                    /**
                     * Бронирование даты в I Rent
                     */
                    $ask = $service->call('Insert_Non_Available_Period', [$__data]);
                    array_push($process_stack, $ask);
                    if ($ask->Insert_Non_Available_PeriodResult->Availability) {
                        /**
                         * Все прошло хорошо, в ответе есть номер заказа
                         **/
                        if (isset($ask->Insert_Non_Available_PeriodResult->Number)) {
                        } else {
                            // ошибка, по каким то причинам заказ не оформлен, смотреть в self::getSoapCallAnswer()
                            $ask->Insert_Non_Available_PeriodResult->Number = '000';
                        }
                        // обновляем календарь забронированных дат
                        $self->createIRentObjectCalendar($item->irent_id, $item->id);
                        // отправляем письма
                        $raw_data['external_id'] = $ask->Insert_Non_Available_PeriodResult->Number;
                        $self->completeOrder($raw_data);
                        $result = true;

                    } else {
                        // не удалоь создать заказ в i rent
                        $result = false;
                    }
                } else {
                    // запрошенный период по каким то причинам не доступен
                    $result = false;
                }
            } else {
                // запрошенный период по каким то причинам не доступен
                $result = false;
            }
        });

        $this->setSoapCallAnswer($process_stack);
        if ($result == false) {
            // обновляем календарь забронированных дат
            $self->createIRentObjectCalendar($item->irent_id, $item->id);
        }

        return $result;
    }

    /**
     * Завершение бронирование, отпаравка почты
     *
     * @param $data
     * @deprecated see BookingDetails::completeOrder()
     */
    public function completeOrder($data)
    {
        $data['type'] = self::TYPE;

        return BookingDetails::completeOrder($data);
    }

    /**
     * @param $result
     */
    protected function setSoapCallAnswer($result)
    {
        $this->soap_call_answer = $result;
    }

    /**
     * @return array
     */
    public function getSoapCallAnswer()
    {
        return $this->soap_call_answer;
    }

    /**
     *
     */
    public function insertCountry()
    {
        $country = file($_SERVER['DOCUMENT_ROOT'] . '/country');
        foreach ($country as $item) {
            $item = explode("	", $item);
            Country::firstOrCreate([
                'name' => trim($item[0]),
                'code' => trim($item[1]),
                'data' => json_encode([
                    'name_ru' => trim($item[0]),
                    'name_en' => trim($item[0]),
                    'name_es' => trim($item[0]),
                ])
            ]);
        }
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: dg
 * Date: 02.11.2017
 * Time: 12:20
 *
 * openssl genpkey -algorithm RSA -out C:\OSPanel\domains\koloboom.local\bitrix\modules\fd.koloboom\lib\buka\keys\private_key.pem -pkeyopt rsa_keygen_bits:102
 *
 */

namespace Fd\Koloboom\Buka;

use Bitrix\Main\Mail\Event;
use Bitrix\Main\Web\Json;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Order;
use CIBlockElement;
use COption;
use CSaleOrder;
use Curl\Curl;
use CUser;
use DateTime;
use SimpleXMLElement;


/**
 * Работа с АПИ Бука и Инфоблоками
 *
 * @package Fd\Koloboom\Buka
 */
class Api
{
    /**
     * Режим отладки
     * @const boolean
     */
    const debug = true;

    /**
     * Идентификатор модуля
     * @const string
     */
    const module_id = 'fd.koloboom';

    /**
     * Версия АПИ Бука
     * @const string
     */
    const version = '1.0';

    /**
     * Уникальное имя пользователя
     * @const string
     */
    const username = '';

    /**
     * Адрес протокола запроса данных на скрипт сообщений
     * @const string
     */
    const address = 'https://partners.buka.ru/protocol';

    /**
     * сообщение об изменении информации в доступных вам каталогах (изменение информации о лицензии, цене, цене со скидкой и т.д.)
     * @const string
     */
    const MESSAGE_CATALOGCHANGE = 'MESSAGE_CATALOGCHANGE';

    /**
     * Сообщение об изменении акционных цен (если акция закончилась, то цены равны 0)
     * @const string
     */
    const MESSAGE_CHANGESTOCKS = 'MESSAGE_CHANGESTOCKS';

    /**
     * Сообщение об изменении доступности товара, если у конкретного цифрового товара/лицензии ключи закончились или появились
     * @const string
     */
    const MESSAGE_PRODUCTAVAILABLE = 'MESSAGE_PRODUCTAVAILABLE';

    /**
     * Сообщение о появлении активационных и/или дополнительных ключей у лицензии, то есть изменения, касающиеся непосредственно заказа
     * @const string
     */
    const MESSAGE_PRODUCTKEY = 'MESSAGE_PRODUCTKEY';

    /**
     * Сообщение о снятии товара с продажи
     * @const string
     */
    const MESSAGE_PUBLICATIONOFF = 'MESSAGE_PUBLICATIONOFF';

    /**
     * Определение принятых данных
     *
     * Если вы по какой-либо причине не присылаете в ответ  1, то запрос будет повторяться каждую минуту в течение часа
     * до тех пор, пока вы не ответите 1. Если по прошествии часа от вас не будет получена 1 в ответ на запрос, то отправка
     * сообщения прекращается, и вам на электронную почту будет отправлено сообщение о неисправности.
     * Ответ вашего скрипта сообщений на POST запрос от Partners Buka Ru не должен превышать 30 секунд.
     *
     * @const int
     */
    const STATUS_SUCCESS = 1;

    /**
     * Определение ошибки обработки данных
     * @const int
     */
    const STATUS_ERROR = 0;

    /**
     * Код статуса выполненного заказа в Битриксе
     *
     * Используется в слушателе события изменения состояния заказа OnSaleStatusOrder для
     * ваполнения заказа в АПИ и получения лицензий
     *
     * @const string
     */
    const STATUS_ORDER_COMPLETE = 'F';

    /**
     * Ключ сессии для хранения идентификатора заказа в Буке
     *
     * @const string
     */
    const SESSION_ORDER_ID_NAME = 'ORDER_ID_BUKA';
    /**
     * openssl genpkey -algorithm RSA -out  /lib/buka/keys/private_key.pem -pkeyopt rsa_keygen_bits:1024
     *
     * @var string
     */
    private $private_key = '';

    /**
     * openssl rsa -pubout -in lib/buka/keys/private_key.pem -out lib/buka/keys/public_key.pem
     * @var string
     */
    private $public_key = '';

    /**
     * Массив ошибок в процессе выполнения скрипта
     *
     * Массив так же содержит ошибки переданные АПИ
     *
     * @var array $errors
     */
    private $errors = [];

    /**
     * Имя события для отправки заказанных ключей
     *
     * @const string
     */
    const EVENT_FD_KOLOBOOM_ORDER_ACTIVATION_LICENSE = 'FD_KOLOBOOM_ORDER_ACTIVATION_LICENSE';

    /**
     * Api constructor.
     * @param bool $is_update
     */
    public function __construct($is_update = false)
    {
        if (!defined('ERROR_EMAIL') && self::debug) {
            define('ERROR_EMAIL', 'd.g.dev482@gmail.com');
        }
        $this->private_key = 'file://' . __DIR__ . '/keys/private_rsa.pem';
    }

    /**
     * Обработка публичных запросов от АПИ
     *
     * @param $request
     * @return mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \ErrorException
     * @throws \Exception
     */
    public function init($request)
    {
        if (isset($request['message'])) {
            $request['message'] = json_decode($request['message']);
            if (isset($request['message']->type)) {
                $request['type'] = $request['message']->type;
            }
        }
        if (self::debug) {
            AddMessage2Log('[init] Запрос к скрипту сообщений:' . print_r($request, true), self::module_id);
        }

        if (!defined('LOG_FILENAME') && self::debug) {
            if (!is_dir(__DIR__ . '/log/' . $request['type'] . '/')) {
                mkdir(__DIR__ . '/log/' . $request['type'] . '/');
            }
            define('LOG_FILENAME', __DIR__ . '/log/' . $request['type'] . '/' . date('Y-m-d_H:i:s') . '.log');
        }
        switch ($request['type']) {
            //Сообщение об изменении доступности товара из–за отсутствия или появления ключей у лицензии
            case self::MESSAGE_PRODUCTAVAILABLE:
                if (self::debug) {
                    AddMessage2Log('[init]Запрос от API: ' . print_r($request, true), self::module_id);
                }
                if (isset($request['message']->data)) {
                    foreach ($request['message']->data as $updateItem) {
                        $updateData = [
                            IBlockElement::PROPERTIES_EXTERNAL_ID => $updateItem->id,
                            IBlockElement::IBLOCK_ID => IBlockCatalog::$id,
                            IBlockElement::PROPERTIES_AVAILABLE => $updateItem->available,
                        ];
                        if (self::debug) {
                            AddMessage2Log('[init] Обновление доступного количества товара:' . print_r($updateData, true), self::module_id);
                        }
                        IBlockElement::updateAvailable($updateData);
                    }
                }

                return Json::encode([
                    'status' => self::STATUS_SUCCESS,
                ]);
            case self::MESSAGE_PUBLICATIONOFF:
                if (self::debug) {
                    AddMessage2Log('[init]Запрос от API: ' . print_r($request, true), self::module_id);
                }
                if (isset($request['message']->data)) {
                    foreach ($request['message']->data as $updateItem) {
                        $updateData = [
                            IBlockElement::PROPERTIES_EXTERNAL_ID => $updateItem->id,
                            IBlockElement::IBLOCK_ID => IBlockCatalog::$id,
                            IBlockElement::PROPERTIES_AVAILABLE => 0,
                        ];
                        if (self::debug) {
                            AddMessage2Log('[init] Обновление доступного количества товара:' . print_r($updateData, true), self::module_id);
                        }
                        IBlockElement::updateAvailable($updateData);
                    }
                }

                return Json::encode([
                    'status' => self::STATUS_SUCCESS,
                ]);
            case self::MESSAGE_CHANGESTOCKS://обновление цен на акционные товары
                if (self::debug) {
                    AddMessage2Log('[init] Запрос от API: ' . print_r($request, true), self::module_id);
                }
                if (isset($request['message']->data->stocks)) {
                    foreach ($request['message']->data->stocks as $stock) {
                        if (isset($stock->product_id)) {
                            $updateData = [
                                IBlockElement::PROPERTIES_EXTERNAL_ID => $stock->product_id,
                                IBlockElement::IBLOCK_ID => IBlockCatalog::$id,
                                IBlockElement::PROPERTIES_PRICE_RETAIL_STOCK => $stock->price_retail_stock,
                                IBlockElement::PROPERTIES_PRICE_WHOLESALE_STOCK => $stock->price_wholesale_stock,
                            ];
                            if (isset($request['message']->data->period_to)) {
                                $updateData['period_to'] = DateTime::createFromFormat('Y-m-d H:i:s', $request['message']->data->period_to);
                            }
                            if (self::debug) {
                                AddMessage2Log('[init] Обновление акционных цен:' . print_r($updateData, true), self::module_id);
                            }
                            IBlockElement::updatePrice($updateData);
                        }
                    }
                }

                return Json::encode([
                    'status' => self::STATUS_SUCCESS,
                ]);
            case self::MESSAGE_PRODUCTKEY://получение ключей по предзаказу
                if (isset($request['message']->data->id)) {
                    if ((int)$request['message']->data->id > 0) {
                        $orderItems = OrderApiProductsTable::checkExist([
                            '=EXTERNAL_ID' => (int)$request['message']->data->id,
                        ], true);

                        if (self::debug) {
                            AddMessage2Log('[init] Поиск заказов по внешнему ключу:' . print_r($orderItems, true), self::module_id);
                        }
                        if ($orderItems) {
                            foreach ($orderItems as $orderItem) {
                                $filterExist = [
                                    '=EXTERNAL_ID' => $orderItem['EXTERNAL_ID'],
                                    '=ORDER_ID' => $orderItem['ORDER_ID'],
                                ];
                                if (self::debug) {
                                    AddMessage2Log('[init] Проверка существования лицензии в заказе:' . print_r($filterExist, true), self::module_id);
                                }
                                $existLicense = IBlockElementKeysTable::checkExist($filterExist);
                                if (null === $existLicense) {
                                    $orderId = $this->getBindOrderByApiOrderId($orderItem['ORDER_ID']);
                                    $this->checkOrder($orderId['ORDER_ID']);
                                }
                            }
                        }
                    }
                }

                return Json::encode([
                    'status' => self::STATUS_SUCCESS,
                ]);
            case self::MESSAGE_CATALOGCHANGE:
                $this->getUpdate();

                return Json::encode([
                    'status' => self::STATUS_SUCCESS,
                ]);
            case 'full_catalog':
                $this->getFull();

                return Json::encode([
                    'status' => self::STATUS_SUCCESS,
                ]);
                break;
            case 'test_order':
//                $orderItems = OrderApiProductsTable::checkExist([
//                    '=EXTERNAL_ID' => $_REQUEST['ID'],
//                ], true);

                return Json::encode([
                    'status' => self::STATUS_SUCCESS,
                ]);
                break;
            case 'keys'://генерация ключей
                // self::getKeys();
                break;
            case 'information':
                // $this->information($_REQUEST['id']);
                break;
            default:
                return Json::encode([
                    'status' => self::STATUS_ERROR,
                ]);
        }

        return Json::encode([
            'status' => self::STATUS_ERROR,
        ]);
    }

    /**
     * @param $id
     * @throws \Bitrix\Main\ArgumentException
     * @throws \ErrorException
     */
    public function information($id)
    {
        $curl = new Curl();
        $json = [
            "api_version" => self::version,
            "username" => self::username,
            "function" => "information",
            'param' => $id,
        ];
        $json = $this->checkJson($json, true);
        $data = [
            'json' => $json,
            'signature' => $this->getSignature($json),
        ];
        if (self::debug) {
            AddMessage2Log('[getUpdate] Запрос к API catalog=>update:' . print_r($data, true), self::module_id);
        }
        $result = $curl->post(self::address, $data);

        $result = (object)Json::decode($this->jsonDecrypt($result));

        if (self::debug) {
            AddMessage2Log('[getUpdate] Ответ API catalog=>update: ' . print_r((array)$result, true), self::module_id);
        }
        var_dump($result);
    }


    /**
     * Обновление каталога
     * @throws \ErrorException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function getUpdate()
    {
        $curl = new Curl();
        $json = [
            "api_version" => self::version,
            "username" => self::username,
            "function" => "catalog",
            'param' => "update",
        ];
        $json = $this->checkJson($json, true);
        $data = [
            'json' => $json,
            'signature' => $this->getSignature($json),
        ];
        if (self::debug) {
            AddMessage2Log('[getUpdate] Запрос к API catalog=>update:' . print_r($data, true), self::module_id);
        }
        $result = $curl->post(self::address, $data);

        $result = (object)Json::decode($this->jsonDecrypt($result));

        if (self::debug) {
            AddMessage2Log('[getUpdate] Ответ API catalog=>update: ' . print_r((array)$result, true), self::module_id);
        }

        $arEvents = GetModuleEvents('fd.koloboom', 'OnBukaApiUpdateCatalog', true);

        foreach ($arEvents as $arEvent) {
            ExecuteModuleEvent($arEvent, $result);
        }

        if (count($this->errors) === 0 && (isset($result->status) && $result->status == 1)) {
            if (isset($result->xml)) {
                $this->process($result);
            }
        }
    }

    /**
     * Загрузка полного каталога
     *
     * Во время загрузки производится создание или обновление товаров в инфоблоке каталога Бука
     * @throws \ErrorException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function getFull()
    {
        try {
            $curl = new Curl();
            $json = [
                "api_version" => self::version,
                "username" => self::username,
                "function" => "catalog",
                'param' => "full",
            ];
            if (self::debug) {
                AddMessage2Log('[getFull] Запрос к API catalog=>full:' . print_r($json, true), self::module_id);
            }
            $json = $this->checkJson($json, true);
            $result = $curl->post(self::address, [
                'json' => $json,
                'signature' => $this->getSignature($json),
            ]);
        } catch (\Exception $exception) {
            if (self::debug) {
                AddMessage2Log('[getFull]Ответ API catalog=>full [exceptio]: ' . print_r([
                        'message' => $exception->getMessage(),
                        'code' => $exception->getCode(),
                        'trace' => $exception->getTrace(),
                    ], true), self::module_id);
            }
        }


        $result = (object)Json::decode($this->jsonDecrypt($result));
        if (self::debug) {
            AddMessage2Log('[getFull] Запрос к API catalog=>full:' . print_r($result, true), self::module_id);
        }
        $arEvents = GetModuleEvents('fd.koloboom', 'OnBukaApiFullCatalog', true);

        foreach ($arEvents as $arEvent) {
            ExecuteModuleEvent($arEvent, $result);
        }

        if (count($this->errors) === 0 && (isset($result->status) && $result->status == 1)) {
            if (isset($result->xml)) {
                $this->process($result);
            }
        }
    }

    /**
     * Обработка ответа от АПИ
     *
     * @param $result
     * @throws \ErrorException
     */
    protected function process($result)
    {
        ini_set('max_execution_time', 0);
        $xml = new SimpleXMLElement($result->xml);
        foreach ($xml as $product) {
            /**
             * Парсим данные с xml
             */
            $item = new BukaItem();
            $item->setData($product);
            /**
             * Обработка товара
             */
            $IBlockElement = new IBlockElement();
            $IBlockElement->initData($item->getData());
        }
    }

    /**
     * @param $json
     * @return string|mixed
     */
    public function jsonDecrypt($json)
    {
        $privKey_r = openssl_pkey_get_private('file://' . __DIR__ . '/keys/private_rsa.pem', 'pass');
        openssl_pkey_export($privKey_r, $key);

        if ($key) {
            $chunkSize = ceil(1023 / 8);
            $output = '';
            $i = 0;
            while ($json) {
                $i++;
                $chunk = substr($json, 0, $chunkSize);
                $json = substr($json, $chunkSize);
                if (!openssl_private_decrypt($chunk, $decrypted, $key)) {
                    array_push($this->errors, 'Failed to decrypt data');

                    return false;
                }
                $output .= $decrypted;
            }
            $output = gzuncompress($output);

            return $output;
        } else {
            array_push($this->errors, 'Error load private key!!!');
        }

        return false;
    }

    /**
     * Получение подписи
     *
     * @param $json
     * @return string|bool
     */
    public function getSignature($json)
    {
        $key = openssl_pkey_get_private($this->private_key);
        if ($key) {
            openssl_sign($json, $signature, $key, 'sha256WithRSAEncryption');
            $signature = base64_encode($signature);
        } else {
            array_push($this->errors, 'Error load private key!!!');

            return '';
        }

        return $signature;
    }

    /**
     * Проверка json объекта и приведение его к строке
     *
     * @param $json array|string
     * @param bool $encode применить последовательность методов base64_encode=>urlencode
     *
     * @return array|mixed|string
     * @throws \Bitrix\Main\ArgumentException
     */
    protected function checkJson($json, $encode = false)
    {
        if (!is_string($json)) {
            $json = Json::encode($json);
        }
        if ($encode === true) {
            $json = urlencode(base64_encode($json));
        }

        return $json;
    }

    /**
     * Получение тела POST запроса к api
     *
     * @param $json object|array
     * @return array
     */
    protected function preparePost($json)
    {
        return [
            'json' => $json,
            'signature' => $this->getSignature($json),
        ];
    }

    /**
     * Получение ключей шифрования
     *
     * @return array
     */
    public static function getKeys()
    {
        if (file_exists('file://' . __DIR__ . '/keys/public_rsa.pen')) {
            $pubKey = openssl_pkey_get_public(file_get_contents(__DIR__ . '/keys/public_rsa.pen'));
            $pubKey = openssl_pkey_get_details($pubKey);

            $privKey_r = openssl_pkey_get_private('file://' . __DIR__ . '/keys/private_rsa.pem');
            openssl_pkey_export($privKey_r, $privKey);

            return array("private" => $privKey, "public" => $pubKey['key']);
        }
        $config = array(
            "digest_alg" => "sha1",
            "private_key_bits" => 1024,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        );
        $res = openssl_pkey_new($config);
        openssl_pkey_export_to_file($res, __DIR__ . '/keys/private_rsa.pem');
        openssl_pkey_export($res, $privKey);

        $pubKey = openssl_pkey_get_details($res);
        file_put_contents(__DIR__ . '/keys/public_rsa.pen', $pubKey['key']);

        return array("private" => $privKey, "public" => $pubKey['key']);
    }

    /**
     * Получение и дешифровка полной выгрузки каталога
     *
     * @param $response
     */
    public function OnBukaApiFullCatalog(&$response)
    {
    }

    /**
     * Получение и дешифровка обновлений каталога
     *
     * @param $response
     */
    public function OnBukaApiUpdateCatalog(&$response)
    {
    }

    /**
     * Создание заказа в PARTNERS BUKA RU.
     *
     * @param $order_id
     * @param $items array
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     * @throws \ErrorException
     * @throws \Exception
     */
    protected function orderMake($order_id, $items)
    {
        if (!defined('LOG_FILENAME') && self::debug) {
            define('LOG_FILENAME', __DIR__ . '/log/orders/' . $order_id . '.log');
        }

        $curl = new Curl();
        $json = [
            "api_version" => self::version,
            "username" => self::username,
            "function" => "order",
            "param" => "make",
            "items" => $items,
            "order_id" => $order_id,
        ];

        if (self::debug) {
            AddMessage2Log('[orderMake] Запрос к API order=>make:' . print_r($json, true), self::module_id);
        }
        $json = $this->checkJson($json, true);
        $result = $curl->post(self::address, [
            'json' => $json,
            'signature' => $this->getSignature($json),
        ]);
        $resultString = $this->jsonDecrypt($result);
        if ($resultString) {
            $result = (object)Json::decode($resultString);
            /**
             * Заказ оформлен, нужно сохранить товары на случай возможного предзаказа
             */
            if (isset($result->status) && (int)$result->status === 1) {
                foreach ($items as $product_id => $item) {
                    $itemData = [
                        '=ORDER_ID' => $order_id,
                        '=PRODUCT_ID' => $product_id,
                        '=EXTERNAL_ID' => $item['id'],
                    ];
                    $existOrderItem = OrderApiProductsTable::checkExist($itemData);
                    if (null === $existOrderItem) {
                        $itemData = [
                            'ORDER_ID' => $order_id,
                            'PRODUCT_ID' => $product_id,
                            'EXTERNAL_ID' => $item['id'],
                        ];
                        OrderApiProductsTable::add($itemData);
                    }
                }
            }
            if (self::debug) {
                AddMessage2Log('[orderMake] Ответ API order=>make: ' . print_r((array)$result, true), self::module_id);
            }
            if (isset($result->errors)) {
                AddMessage2Log('[orderMake] Ответ API order=>make, ОШИБКА ОФОРМЛЕНИЯ ЗАКАЗА: ' . PHP_EOL . print_r((array)$result->errors, true), self::module_id);
                if (self::debug) {
                    SendError('[orderMake] Ответ API order=>make, ОШИБКА ОФОРМЛЕНИЯ ЗАКАЗА: ' . PHP_EOL . print_r((array)$result->errors, true));
                }

                $this->errors = $result->errors;
                if (isset($result->errors['error']['code'])) {
                    switch ($result->errors['error']['code']) {
                        case 1://Заказ с таким "order_id" уже существует
                            break;
                        default:
                            break;
                    }
                }
            }
        } else {
            if (self::debug) {
                AddMessage2Log('[orderMake] Ответ API: ошибка декодирования:' . PHP_EOL . print_r([
                        'mbstring.func_overload' => ini_get('mbstring.func_overload'),
                    ], true), self::module_id);
            }
        }


        return (isset($result->status) && (int)$result->status === 1) ? true : false;
    }

    /**
     * Завершение оформления заказа и получение лицензий
     *
     * В случае успешного выполнения возвращает лицензии,
     * в случае ошибки false
     *
     * @param $order_id
     * @return mixed|object
     * @throws \Bitrix\Main\ArgumentException
     * @throws \ErrorException
     */
    protected function orderComplete($order_id)
    {
        $curl = new Curl();
        $json = [
            "api_version" => self::version,
            "username" => self::username,
            "function" => "order",
            "param" => "complete",
            "order_id" => $order_id,
        ];
        if (!defined('LOG_FILENAME') && self::debug) {
            define('LOG_FILENAME', __DIR__ . '/log/orders/' . $order_id . '.log');
        }
        if (self::debug) {
            AddMessage2Log('[orderComplete] Запрос к API order=>complete:' . print_r($json, true), self::module_id);
        }
        $json = $this->checkJson($json, true);
        $result = $curl->post(self::address, [
            'json' => $json,
            'signature' => $this->getSignature($json),
        ]);
        $result = (object)Json::decode($this->jsonDecrypt($result));

        if (self::debug) {
            AddMessage2Log('[orderComplete] Ответ API order=>complete: ' . print_r((array)$result, true), self::module_id);
        }
        if (isset($result->status)) {
            switch ($result->status) {
                case self::STATUS_SUCCESS:
                    return $result;
                case self::STATUS_ERROR:
                    if (isset($result->errors)) {
                        AddMessage2Log('[orderComplete] Ответ API order=>complete, ОШИБКА ПОЛУЧЕНИЯ ЗАКАЗА: ' . PHP_EOL . print_r((array)$result->errors, true), self::module_id);

                        if (self::debug) {
                            SendError('[orderComplete] Ответ API order=>complete, ОШИБКА ПОЛУЧЕНИЯ ЗАКАЗА: ' . PHP_EOL . print_r((array)$result->errors, true));
                        }
                    }
                    break;
            }
        }

        return false;
    }

    /**
     * Создание заказа в PARTNERS BUKA RU.
     *
     * @param $id int Идентификатор заказа
     * @param $status string Идентификатор статуса, смотреть КОД статуса в админке
     * @throws \Bitrix\Main\ArgumentException
     * @throws \ErrorException
     * @throws \Exception
     */
    public function OnSaleStatusOrder($id, $status)
    {
        $api = new Api();
        $api->clearOrderId();

        if (self::debug) {
            AddMessage2Log('[OnSaleStatusOrder] Изменение состояния заказа: ' . $status, self::module_id);
        }
        switch ($status) {
            case self::STATUS_ORDER_COMPLETE://заказ выполнен, нужно получить лицензии
                /**
                 * Массив полученных ключей NAME=>'ABC',KEY=>'XYZ'
                 * @var $result array
                 */
                $result = $api->checkOrder($id);

                if (self::debug) {
                    AddMessage2Log('[OnSaleStatusOrder] Результат проверки заказа #' . $id . ' в Буке: ' . print_r($result, true), self::module_id);
                }

                if (count($result)) {

                    $sendString = '<ul style="list-style: decimal">';
                    foreach ($result as $item) {
                        $sendString .= '<li>' . $item['NAME'] . ': ' . $item['KEY'] . '</li>';
                    }
                    $sendString .= '</ul>';
                    $order = new CSaleOrder();

                    if ($arOrder = $order->GetByID($id)) {
                        if ($user = CUser::GetByID($arOrder['USER_ID'])->fetch()) {

                            Event::send(array(
                                "EVENT_NAME" => Api::EVENT_FD_KOLOBOOM_ORDER_ACTIVATION_LICENSE,
                                "LID" => $arOrder["LID"],
                                "C_FIELDS" => [
                                    "SUBJECT" => "Заказ №" . $id . " выполнен.",
                                    "EMAIL_TO" => $user['EMAIL'],
                                    "DEFAULT_EMAIL_FROM" => COption::GetOptionString("sale", "order_email", "order@koloboom.ru"),
                                    "USER_ID" => $arOrder['USER_ID'],
                                    "ORDER_ID" => $id,
                                    "ACTIVATION_KEYS" => $sendString,
                                ],
                            ));
                        }
                    }
                }

                break;
        }
    }

    /**
     * Проверка и получение заказа от Буки
     *
     * Метод возвращает массив ключей для дальнейшей обработки
     *
     * @param int $id Идентификатор заказа Битрикса
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \ErrorException
     * @throws \Exception
     */
    private function checkOrder($id)
    {
        $api = $this;
        $result = [];
        $filter = [
            'filter' => ['ORDER_ID' => $id],
        ];
        $obBasket = Basket::getList($filter);
        if (self::debug) {
            AddMessage2Log('[checkOrder] Получение товаров заказа по фильтру:' . print_r($filter, true), self::module_id);
        }
        $localItems = [];//парамтеры заказа товара у АПИ
        while ($bItem = $obBasket->Fetch()) {//получаем текущую корзину
            $rIBlockElement = CIBlockElement::GetByID($bItem['PRODUCT_ID']);
            if ($obIBlockElement = $rIBlockElement->GetNextElement()) {//получаем элемент инфоблока
                $arIBlockElementProperties = $obIBlockElement->GetProperties();
                $arIBlockElementFields = $obIBlockElement->GetFields();
                if (isset($arIBlockElementProperties['EXTERNAL_ID'])) {
                    $localItems[(int)$arIBlockElementProperties['EXTERNAL_ID']['VALUE']] = [
                        'ID' => $arIBlockElementFields['ID'],
                        'NAME' => $arIBlockElementFields['NAME'],
                        'EXTERNAL_ID' => (int)$arIBlockElementProperties['EXTERNAL_ID']['VALUE'],
                    ];
                }
            }
        }
        if (self::debug) {
            AddMessage2Log('[checkOrder] Результат получения товаров заказа по фильтру:' . print_r($localItems, true), self::module_id);
        }
        $apiOrderId = $api->getBindOrderByOrderId($id);

        if ($apiOrderId['ORDER_API_ID']) {//делаем заказ в АПИ Бука
            $orderComplete = $api->orderComplete($apiOrderId['ORDER_API_ID']);//получаем заказ от АПИ Бука
            if ($orderComplete !== false) {
                switch ($orderComplete->status) {
                    case self::STATUS_SUCCESS:
                        if (isset($orderComplete->data)) {
                            foreach ($orderComplete->data['keys'] as $item_id => $value) {
                                if (isset($localItems[$item_id])) {
                                    $value = current($value);
                                    /**
                                     * Добавление лицензии
                                     */
                                    if ($value['activation']) {
                                        array_push($result, [//добавляем товар в результаты для отправки почтой
                                            'NAME' => $localItems[$item_id]['NAME'],
                                            'KEY' => $value['activation'],
                                        ]);
                                        $api->localStore($apiOrderId['ORDER_API_ID'], $localItems[$item_id], $value['activation']);
                                    }
                                    if (isset($value['additional']) && count($value['additional']) > 0) {
                                        foreach ($value['additional'] as $item) {
                                            $localItems[$item_id]['NAME'] = $item['name'];
                                            $api->localStore($apiOrderId['ORDER_API_ID'], $localItems[$item_id], $item['key']);
                                            array_push($result, [//добавляем товар в результаты для отправки почтой
                                                'NAME' => $localItems[$item_id]['NAME'],
                                                'KEY' => $item['key'],
                                            ]);
                                        }
                                    }
                                }
                            }
                        }
                        break;
                    case self::STATUS_ERROR:
                        AddMessage2Log('[checkOrder] ОШИБКА ПОЛУЧЕНИЯ ЗАКАЗА: ' . PHP_EOL . print_r((array)$orderComplete, true), $api->MODULE_ID);
                        if (self::debug) {
                            SendError('[Event#OnSaleStatusOrder] ОШИБКА ПОЛУЧЕНИЯ ЗАКАЗА: ' . PHP_EOL . print_r((array)$orderComplete, true));
                        }
                        break;
                }
            }
        } else {
            AddMessage2Log('[checkOrder] ОШИБКА ПОЛУЧЕНИЯ ПРИВЯЗКИ ЗАКЗАОВ: ' . PHP_EOL . print_r(['order_id' => $id], true), Api::module_id);
            if (self::debug) {
                SendError('[checkOrder] ОШИБКА ПОЛУЧЕНИЯ ЗАКАЗА: ' . PHP_EOL . print_r(['order_id' => $id], true));
            }
        }

        return $result;
    }

    /**
     * Происходит в самом начале процесса сохранения.
     *
     * @param Order $order
     * @param $values array
     * @return \Bitrix\Main\EventResult
     * @throws \Bitrix\Main\ArgumentException
     * @throws \ErrorException
     * @throws \Exception
     */
    public function OnSaleOrderBeforeSaved(Order $order, $values)
    {
        $id = $order->getId();

        if ($id === 0) {//при сохранение НОВОГО заказа ползователем

            $basket = $order->getBasket();

            $items = [];//парамтеры заказа товара у АПИ
            $localItems = [];//парамтеры заказа товара у АПИ
            $basketItems = $basket->getBasketItems();
            /**
             * @var $bItem \Bitrix\Sale\BasketItem
             */
            foreach ($basketItems as $bItem) {//получаем текущую корзину
                $rIBlockElement = CIBlockElement::GetByID($bItem->getProductId());
                if ($obIBlockElement = $rIBlockElement->GetNextElement()) {//получаем элемент инфоблока
                    $arIBlockElementProperties = $obIBlockElement->GetProperties();
                    $arIBlockElementFields = $obIBlockElement->GetFields();
                    if (isset($arIBlockElementProperties['EXTERNAL_ID'])) {
                        $localItems[(int)$arIBlockElementProperties['EXTERNAL_ID']['VALUE']] = [
                            'ID' => $arIBlockElementFields['ID'],
                            'NAME' => $arIBlockElementFields['NAME'],
                        ];
                        $items[$arIBlockElementFields['ID']] = [//заполняем массив для заказа в АПИ
                            'id' => (int)$arIBlockElementProperties['EXTERNAL_ID']['VALUE'],
                            'price_retail' => (int)$arIBlockElementProperties['PRICE_RETAIL']['VALUE'],
                            'price_wholesale' => (int)$arIBlockElementProperties['PRICE_WHOLESALE']['VALUE'],
                            'price_retail_stock' => (int)$arIBlockElementProperties['PRICE_RETAIL_STOCK']['VALUE'],
                            'price_wholesale_stock' => (int)$arIBlockElementProperties['PRICE_WHOLESALE_STOCK']['VALUE'],
                        ];
                    }
                }
            }

            if (count($items)) {//делаем заказ в АПИ Бука
                $api = new Api();
                $api->errors = [];
                $orderMake = $api->orderMake($api->getApiOrderId(), $items);

                if (true !== $orderMake) {
                    $errorText = 'В процессе обработки заказа произошла ошибка';
                    switch ($api->errors['error']['code']) {
                        case 1:
                            $errorText .= ', обновите страницу и попробуйте оформить заказ еще раз.';
                            break;
                        case 2:
                        case 8://Данный продукт недоступен в вашем каталоге
                            if (isset($api->errors['error']['product_id']) && isset($localItems[$api->errors['error']['product_id']])) {
                                $errorText .= ': "' . $localItems[$api->errors['error']['product_id']]['NAME'] . '" больше не доступен к покупке';
                            }
                            break;

                        default:
                            $errorText = 'В процессе обработки заказа произошла ошибка.';
                            break;
                    }
                    $api->clearOrderId();

                    return new \Bitrix\Main\EventResult(
                        \Bitrix\Main\EventResult::ERROR,
                        new \Bitrix\Sale\ResultError($errorText, 'SALE_EVENT_WRONG_ORDER'),
                        'sale'
                    );
                }
            }
        }

    }

    /**
     * Происходит в конце сохранения, когда заказ и все связанные сущности уже сохранены.
     *
     * @param $order Order
     * @param $values array Старые значения полей заказа.
     * @param $is_new
     *
     * @return void
     * @throws \Exception
     * @internal param \Bitrix\Main\Event $event
     */
    public function OnSaleOrderSaved(Order $order, $values, $is_new)
    {
        /**
         * Только в случае существования идентификатора заказа Буки в сессии
         */
        if (isset($_SESSION[self::SESSION_ORDER_ID_NAME]) && $_SESSION[self::SESSION_ORDER_ID_NAME] !== null) {
            $api = new Api();
            $api->bindOrderId($order->getId());//Связываем значения Идентификатор заказа Битрикс и Идентификатор заказа Бука
        }
    }

    /**
     * Сохранение лицензий в базу данных с привязкой к заказу и товару
     *
     * @param $id int идентификатор заказа
     * @param $localItem array массив содержащий ID, EXTERNAL_ID и NAME товара
     * @param $value string лицензионный ключ
     * @throws \Exception
     */
    private function localStore($id, $localItem, $value)
    {
        $existActivation = IBlockElementKeysTable::checkExist([
            '=ORDER_ID' => $id,
            '=PRODUCT_ID' => $localItem['ID'],
            '=VALUE' => $value,
        ]);
        $data = [
            'ORDER_ID' => $id,
            'PRODUCT_ID' => $localItem['ID'],
            'EXTERNAL_ID' => $localItem['EXTERNAL_ID'],
            'VALUE' => $value,
            'NAME' => $localItem['NAME'],
        ];
        if (null === $existActivation) {
            IBlockElementKeysTable::add($data);
            if (self::debug) {
                AddMessage2Log('[localStore] Добавлена новая лицензия: ' . PHP_EOL . print_r($data, true), self::module_id);
            }
        } else {
            IBlockElementKeysTable::getById($existActivation);
            if (self::debug) {
                AddMessage2Log('[localStore] Определена лицензия по заказу: ' . PHP_EOL . print_r($data, true), self::module_id);
            }
        }
    }

    /**
     * Возвращает идентификатор заказа в Буке
     *
     * @return mixed
     */
    private function getApiOrderId()
    {
        if (isset($_SESSION[self::SESSION_ORDER_ID_NAME]) && $_SESSION[self::SESSION_ORDER_ID_NAME] !== null) {
            return $_SESSION[self::SESSION_ORDER_ID_NAME];
        }
        $_SESSION[self::SESSION_ORDER_ID_NAME] = time();

        return $_SESSION[self::SESSION_ORDER_ID_NAME];
    }

    /**
     * Удаляет идентификатор закза из сессии
     *
     * @return bool
     */
    public function clearOrderId()
    {
        $_SESSION[self::SESSION_ORDER_ID_NAME] = null;
        unset($_SESSION[self::SESSION_ORDER_ID_NAME]);

        return (empty($_SESSION[self::SESSION_ORDER_ID_NAME]));
    }

    /**
     * Связываение ИД заказов Битрикс и Бука
     *
     * @param $id
     * @throws \Exception
     */
    public function bindOrderId($id)
    {
        OrderApiTable::add([
            'ORDER_ID' => $id,
            'ORDER_API_ID' => $this->getApiOrderId(),
        ]);
        $this->clearOrderId();//очищаем сессию
    }

    /**
     * Возвращает привязку заказов Битрикс и Бука по идентификатору битрикса
     *
     * @param $order_id
     * @return array|false
     */
    public function getBindOrderByOrderId($order_id)
    {
        $filter = [
            'ORDER_ID' => $order_id,
        ];
        if (self::debug) {
            AddMessage2Log('[getBindOrderByOrderId] Получение привязки заказов по фильтру: ' . PHP_EOL . print_r($filter, true), self::module_id);
        }

        $result = OrderApiTable::checkExist($filter);

        if (self::debug) {
            AddMessage2Log('[getBindOrderByOrderId] Результат получения привязки заказов по фильтру: ' . PHP_EOL . print_r($result, true), self::module_id);
        }

        return $result;
    }

    /**
     * Возвращает привязку заказов Битрикс и Бука по идентификатору Бука
     *
     * @param $order_id
     * @return array|false
     */
    public function getBindOrderByApiOrderId($order_id)
    {
        $filter = [
            'ORDER_API_ID' => $order_id,
        ];
        if (self::debug) {
            AddMessage2Log('[getBindOrderByApiOrderId] Получение привязки заказов по фильтру: ' . PHP_EOL . print_r($filter, true), self::module_id);
        }

        $result = OrderApiTable::checkExist($filter);

        if (self::debug) {
            AddMessage2Log('[getBindOrderByApiOrderId] Результат получения привязки заказов по фильтру: ' . PHP_EOL . print_r($result, true), self::module_id);
        }

        return $result;
    }
}
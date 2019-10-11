<?php
/**
 * Created by PhpStorm.
 * User: dg
 * Date: 09.11.2017
 * Time: 15:38
 */

namespace Fd\Koloboom\Buka;

use Bitrix\Main\Type\DateTime;
use CCatalogDiscount;
use CCatalogProduct;
use CFile;
use CIBlockElement;
use CPrice;
use Curl\Curl;
use CUtil;

/**
 * Элемент Инфоблока каталога БУКА
 *
 * @package Fd\Koloboom\Buka
 */
class IBlockElement
{
    /**
     * Идентификатор модуля
     *
     * @var string $MODULE_ID
     */
    public $MODULE_ID = 'fd.koloboom';
    /**
     * Размещение изображений
     *
     * @var string $savePath
     */
    private $savePath = 'games';

    /**
     * ID информационного блока.
     *
     * @const string
     */
    const IBLOCK_ID = 'IBLOCK_ID';

    /**
     * ID группы. Если не задан, то элемент не привязан к группе. Если элемент привязан к нескольким группам,
     * то в этом поле ID одной из групп. По умолчанию содержит привязку к разделу с минимальным ID.
     *
     * @const string
     */
    const IBLOCK_SECTION_ID = 'IBLOCK_SECTION';

    /**
     * Название элемента.
     *
     * @const string
     */
    const NAME = 'NAME';

    /**
     * Флаг активности (Y|N).
     *
     * @const string
     */
    const ACTIVE = 'ACTIVE';

    /**
     * Код картинки в таблице файлов для предварительного просмотра (анонса).
     *
     * @const string
     */
    const PREVIEW_PICTURE = 'PREVIEW_PICTURE';

    /**
     * Предварительное описание (анонс).
     *
     * @const string
     */
    const PREVIEW_TEXT = 'PREVIEW_TEXT';

    /**
     * Тип предварительного описания (text/html).
     *
     * @const string
     */
    const PREVIEW_TEXT_TYPE = 'PREVIEW_TEXT_TYPE';

    /**
     * Массив со всеми значениями свойств элемента в виде массива Array("код свойства"=>"значение свойства").
     * Где "код свойства" - числовой или символьный код свойства, "значение свойства" - одиночное значение,
     * либо массив значений если свойство множественное.
     *
     * @const string
     */
    const PROPERTY_VALUES = 'PROPERTY_VALUES';

    /**
     * Идентификатор продукта в системе API Partners Buka Ru
     *
     * @const string
     */
    const PROPERTIES_EXTERNAL_ID = 'EXTERNAL_ID';

    /**
     * Дата последнего изменения
     *
     * @const string
     */
    const PROPERTIES_DATETIME_CHANGE = 'DATETIME_CHANGE';
    /**
     * Дата начала продаж продукта
     *
     * @const string
     */
    const PROPERTIES_DATETIME_REALASE = 'DATETIME_REALASE';

    /**
     * Рекомендованная розничная цена
     *
     * @const string
     */
    const PROPERTIES_PRICE_RETAIL = 'PRICE_RETAIL';

    /**
     * Рекомендованная оптовая цена
     *
     * @const string
     */
    const PROPERTIES_PRICE_WHOLESALE = 'PRICE_WHOLESALE';

    /**
     * Рекомендованная розничная цена со скидкой
     *
     * @const string
     */
    const PROPERTIES_PRICE_RETAIL_STOCK = 'PRICE_RETAIL_STOCK';

    /**
     * Рекомендованная оптовая цена со скидкой
     *
     * @const string
     */
    const PROPERTIES_PRICE_WHOLESALE_STOCK = 'PRICE_WHOLESALE_STOCK';

    /**
     * Удаление продукта (0/1)
     *
     * @const string
     */
    const PROPERTIES_ARCHIVE = 'ARCHIVE';

    /**
     * Публикация продукта
     *
     * @const string
     */
    const PROPERTIES_PUBLICATION = 'PUBLICATION';

    /**
     * Наличие ключей
     *
     * @const string
     */
    const PROPERTIES_AVAILABLE = 'AVAILABLE';

    /**
     * Флаг релиза
     *
     * @const string
     */
    const PROPERTIES_RELEASE = 'RELEASE';

    /**
     * Видео (код из Youtube)
     *
     * @const string
     */
    const VIDEO_YOUTUBE = 'VIDEO_YOUTUBE';

    /**
     * Наименование продукта на английском языке
     *
     * @const string
     */
    const PROPERTIES_NAME_ENG = 'NAME_ENG';

    /**
     * Название операционных(ой)систем(ы)
     *
     * @const string
     */
    const PROPERTIES_SYS_MIN_OS = 'SYS_MIN_OS';

    /**
     * Название процессоров(а)
     *
     * @const string
     */
    const PROPERTIES_SYS_MIN_PROCESSOR = 'SYS_MIN_PROCESSOR';

    /**
     * Требования к размеру оперативной памяти
     *
     * @const string
     */
    const PROPERTIES_SYS_MIN_RAM = 'SYS_MIN_RAM';

    /**
     * Название видеокарт(ы)
     *
     * @const string
     */
    const PROPERTIES_SYS_MIN_VIDEO_CARD = 'SYS_MIN_VIDEO_CARD';

    /**
     * Требования к версии DirectX
     *
     * @const string
     */
    const PROPERTIES_SYS_MIN_DIRECTX = 'SYS_MIN_DIRECTX';

    /**
     * Требования к звуковой карте
     *
     * @const string
     */
    const PROPERTIES_SYS_MIN_SOUND_CARD = 'SYS_MIN_SOUND_CARD';

    /**
     * Сведения о необходимом свободном пространстве на жестком диске
     *
     * @const string
     */
    const PROPERTIES_SYS_MIN_HDD = 'SYS_MIN_HDD';

    /**
     * Требования к интернет подключению
     *
     * @const string
     */
    const PROPERTIES_SYS_MIN_INTERNET = 'SYS_MIN_INTERNET';

    /**
     * Дополнительная информация
     *
     * @const string
     */
    const PROPERTIES_SYS_MIN_ADDITIONALLY = 'SYS_MIN_ADDITIONALLY';

    /**
     * Название операционных(ой)систем(ы) Mac
     *
     * @const string
     */
    const PROPERTIES_SYS_MIN_MAC_OS = 'SYS_MIN_MAC_OS';

    /**
     * Название процессоров(а) Mac
     *
     * @const string
     */
    const PROPERTIES_SYS_MIN_MAC_PROCESSOR = 'SYS_MIN_MAC_PROCESSOR';

    /**
     * Требования к размеру оперативной памяти Mac
     *
     * @const string
     */
    const PROPERTIES_SYS_MIN_MAC_RAM = 'SYS_MIN_MAC_RAM';

    /**
     * Название видеокарт(ы) Mac
     *
     * @const string
     */
    const PROPERTIES_SYS_MIN_MAC_VIDEO_CARD = 'SYS_MIN_MAC_VIDEO_CARD';

    /**
     * Требования к версии DirectX Mac
     *
     * @const string
     */
    const PROPERTIES_SYS_MIN_MAC_DIRECTX = 'SYS_MIN_MAC_DIRECTX';

    /**
     * Требования к звуковой карте Mac
     *
     * @const string
     */
    const PROPERTIES_SYS_MIN_MAC_SOUND_CARD = 'SYS_MIN_MAC_SOUND_CARD';

    /**
     * Сведения о необходимом свободном пространстве на жестком диске Mac
     *
     * @const string
     */
    const PROPERTIES_SYS_MIN_MAC_HDD = 'SYS_MIN_MAC_HDD';

    /**
     * Требования к интернет подключению Mac
     *
     * @const string
     */
    const PROPERTIES_SYS_MIN_MAC_INTERNET = 'SYS_MIN_MAC_INTERNET';

    /**
     * Дополнительная информация Mac
     *
     * @const string
     */
    const PROPERTIES_SYS_MIN_MAC_ADDITIONALLY = 'SYS_MIN_MAC_ADDITIONALLY';

    /**
     * Название операционных(ой)систем(ы)
     *
     * @const string
     */
    const PROPERTIES_SYS_REQ_OS = 'SYS_REQ_OS';

    /**
     * Название процессоров(а)
     *
     * @const string
     */
    const PROPERTIES_SYS_REQ_PROCESSOR = 'SYS_REQ_PROCESSOR';

    /**
     * Требования к размеру оперативной памяти
     *
     * @const string
     */
    const PROPERTIES_SYS_REQ_RAM = 'SYS_REQ_RAM';

    /**
     * Требования к версии DirectX
     *
     * @const string
     */
    const PROPERTIES_SYS_REQ_DIRECTX = 'SYS_REQ_DIRECTX';

    /**
     * Требования к звуковой карте
     *
     * @const string
     */
    const PROPERTIES_SYS_REQ_SOUND_CARD = 'SYS_REQ_SOUND_CARD';

    /**
     * Название видеокарт(ы)
     *
     * @const string
     */
    const PROPERTIES_SYS_REQ_VIDEO_CARD = 'SYS_REQ_VIDEO_CARD';

    /**
     * Сведения о необходимом свободном пространстве на жестком диске
     *
     * @const string
     */
    const PROPERTIES_SYS_REQ_HDD = 'SYS_REQ_HDD';

    /**
     * Требования к интернет подключению
     *
     * @const string
     */
    const PROPERTIES_SYS_REQ_INTERNET = 'SYS_REQ_INTERNET';

    /**
     * Дополнительная информация
     *
     * @const string
     */
    const PROPERTIES_SYS_REQ_ADDITIONALLY = 'SYS_REQ_ADDITIONALLY';

    /**
     * Название операционных(ой)систем(ы) Mac
     *
     * @const string
     */
    const PROPERTIES_SYS_REQ_MAC_OS = 'SYS_REQ_MAC_OS';

    /**
     * Название процессоров(а) Mac
     *
     * @const string
     */
    const PROPERTIES_SYS_REQ_MAC_PROCESSOR = 'SYS_REQ_MAC_PROCESSOR';

    /**
     * Требования к размеру оперативной памяти Mac
     *
     * @const string
     */
    const PROPERTIES_SYS_REQ_MAC_RAM = 'SYS_REQ_MAC_RAM';

    /**
     * Требования к версии DirectX Mac
     *
     * @const string
     */
    const PROPERTIES_SYS_REQ_MAC_DIRECTX = 'SYS_REQ_MAC_DIRECTX';

    /**
     * Требования к звуковой карте Mac
     *
     * @const string
     */
    const PROPERTIES_SYS_REQ_MAC_SOUND_CARD = 'SYS_REQ_MAC_SOUND_CARD';

    /**
     * Название видеокарт(ы) Mac
     *
     * @const string
     */
    const PROPERTIES_SYS_REQ_MAC_VIDEO_CARD = 'SYS_REQ_MAC_VIDEO_CARD';

    /**
     * Сведения о необходимом свободном пространстве на жестком диске Mac
     *
     * @const string
     */
    const PROPERTIES_SYS_REQ_MAC_HDD = 'SYS_REQ_MAC_HDD';

    /**
     * Требования к интернет подключению Mac
     *
     * @const string
     */
    const PROPERTIES_SYS_REQ_MAC_INTERNET = 'SYS_REQ_MAC_INTERNET';

    /**
     * Дополнительная информация Mac
     *
     * @const string
     */
    const PROPERTIES_SYS_REQ_MAC_ADDITIONALLY = 'SYS_REQ_MAC_ADDITIONALLY';

    /**
     * Жанр
     *
     * @const string
     */
    const PROPERTIES_ADDITION_INFORMATION_GENRE = 'ADDITION_INFORMATION_GENRE';

    /**
     * Разработчик
     *
     * @const string
     */
    const PROPERTIES_ADDITION_INFORMATION_DEVELOPER = 'ADDITION_INFORMATION_DEVELOPER';

    /**
     * Издатель
     *
     * @const string
     */
    const PROPERTIES_ADDITION_INFORMATION_PUBLISHER = 'ADDITION_INFORMATION_PUBLISHER';

    /**
     * Издатель в РФ
     *
     * @const string
     */
    const PROPERTIES_ADDITION_INFORMATION_PUBLISHER_RUSSIAN = 'ADDITION_INFORMATION_PUBLISHER_RUSSIAN';

    /**
     * Возрастное ограничение
     *
     * @const string
     */
    const PROPERTIES_ADDITION_INFORMATION_AGERATING = 'ADDITION_INFORMATION_AGERATING';

    /**
     * Локализация
     *
     * @const string
     */
    const PROPERTIES_ADDITION_INFORMATION_LOCALIZATION = 'ADDITION_INFORMATION_LOCALIZATION';

    /**
     * Платформы(а)
     *
     * @const string
     */
    const PROPERTIES_ADDITION_INFORMATION_PLATFORM = 'ADDITION_INFORMATION_PLATFORM';

    /**
     * Система активации
     *
     * @const string
     */
    const PROPERTIES_ADDITION_INFORMATION_SYSTEM_ACTIVATION = 'ADDITION_INFORMATION_SYSTEM_ACTIVATION';

    /**
     * Категория
     *
     * @const string
     */
    const PROPERTIES_ADDITION_INFORMATION_CATEGORY = 'ADDITION_INFORMATION_CATEGORY';

    /**
     * Регионы
     *
     * multiple
     *
     * @const string
     */
    const PROPERTIES_ADDITION_INFORMATION_REGIONS = 'ADDITION_INFORMATION_REGIONS';

    /**
     * Фото
     *
     * @const string
     */
    const PROPERTIES_IMAGES_MAIN = 'IMAGES_MAIN';

    /**
     * Скриншоты
     *
     * @const string
     */
    const PROPERTIES_IMAGES_SCREENSHOT = 'IMAGES_SCREENSHOT';

    /**
     * Спец. статусы
     *
     * @const string
     */
    const PROPERTIES_HIT = 'HIT';

    /**
     * Поле используемое для определения цены товара
     *
     * @const string
     */
    const PRICE_FIELD = 'PRICE_RETAIL';//price_retail (число с плавающей точкой) - рекомендованная розничная цена

    /**
     * Код типа цены.
     *
     * @const int
     */
    const CATALOG_GROUP_ID = 1;

    const IMAGE_750_470 = 'IMAGE_750_470';

    const IMAGE_750_580 = 'IMAGE_750_580';

    /**
     * Заглушка на отсутсвующее фото
     *
     * @var string|int $no_photo_image
     */
    protected $no_photo_image = '/bitrix/templates/aspro_next/images/no_photo_medium.png';

    /**
     * @var $transport Curl
     */
    protected $transport;

    /**
     * @var int $redirectCounter
     */
    protected $redirectCounter = 0;


    /**
     * @var string
     */
    protected $userAgent = 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.125 Safari/537.36';


    /**
     * IBlockElement constructor.
     * @throws \ErrorException
     */
    public function __construct()
    {
        if (!defined('LOG_FILENAME')) {
            define('LOG_FILENAME', __DIR__ . '/log/buka.log');
        }

        $this->transport = new Curl();
        /**
         * Проверяем заглушку
         */
        $res = CFile::GetList([
            "FILE_SIZE" => "desc",
        ], [
            "MODULE_ID" => Api::module_id,
            "ORIGINAL_NAME" => 'no_photo.jpg',
        ]);
        $res_arr = $res->GetNext();

        if (!$res_arr) {
            $this->no_photo_image = CFile::SaveFile([
                'name' => 'no_photo.jpg',
                'type' => 'image/jpg',
                'tmp_name' => $_SERVER['DOCUMENT_ROOT'] . $this->no_photo_image,
                'MODULE_ID' => Api::module_id,
            ], 'catalog');
        } else {
            $this->no_photo_image = $res_arr['ID'];
        }

    }

    /**
     * Определение данных объекта инфоблока
     *
     * @param array $data
     */
    public function initData(array $data)
    {
        global $APPLICATION;

        $iBlockSection = new IBlockSection();
        $iBlockElement = new CIBlockElement();

        $imagesMain = $this->getImagesMain($data[BukaItem::TAG_IMAGES]);
        $images = $data[BukaItem::TAG_IMAGES];


        $arParams = array("replace_space" => "-", "replace_other" => "-");

        $data = [
            'ID' => 0,
            'CODE' => Cutil::translit($data[BukaItem::NAME], "ru", $arParams),
            self::NAME => $data[BukaItem::NAME],
            self::PREVIEW_PICTURE => null,
            self::PREVIEW_TEXT_TYPE => 'html',
            self::PREVIEW_TEXT => $data[BukaItem::DESCRIPTION],
            self::IBLOCK_ID => IBlockCatalog::$id,
            self::IBLOCK_SECTION_ID => $iBlockSection->check($data),
            self::ACTIVE => (($data[BukaItem::ARCHIVE] == 1 && $data[BukaItem::PUBLICATION] == 0) || ($data[BukaItem::PUBLICATION] == 0)) ? 'N' : 'Y',
            self::PROPERTY_VALUES => [
                self::PROPERTIES_EXTERNAL_ID => $data[BukaItem::ID],
                self::PROPERTIES_DATETIME_CHANGE => $data[BukaItem::DATETIME_CHANGE],
                self::PROPERTIES_DATETIME_REALASE => $data[BukaItem::DATETIME_REALASE],
                self::PROPERTIES_PRICE_RETAIL => (float)$data[BukaItem::PRICE_RETAIL],
                self::PROPERTIES_PRICE_WHOLESALE => (float)$data[BukaItem::PRICE_WHOLESALE],
                self::PROPERTIES_PRICE_RETAIL_STOCK => (float)$data[BukaItem::PRICE_RETAIL_STOCK],
                self::PROPERTIES_PRICE_WHOLESALE_STOCK => (float)$data[BukaItem::PRICE_WHOLESALE_STOCK],
                self::PROPERTIES_ARCHIVE => $data[BukaItem::ARCHIVE],
                self::PROPERTIES_PUBLICATION => $data[BukaItem::PUBLICATION],
                self::PROPERTIES_AVAILABLE => $data[BukaItem::AVAILABLE],
                self::PROPERTIES_RELEASE => $data[BukaItem::RELEASE],
                self::PROPERTIES_NAME_ENG => $data[BukaItem::NAME_ENG],
                self::PROPERTIES_SYS_MIN_OS => $data[BukaItem::TAG_SYS_MIN]['os'],
                self::PROPERTIES_SYS_MIN_PROCESSOR => $data[BukaItem::TAG_SYS_MIN]['processor'],
                self::PROPERTIES_SYS_MIN_RAM => $data[BukaItem::TAG_SYS_MIN]['ram'],
                self::PROPERTIES_SYS_MIN_VIDEO_CARD => $data[BukaItem::TAG_SYS_MIN]['video_card'],
                self::PROPERTIES_SYS_MIN_DIRECTX => $data[BukaItem::TAG_SYS_MIN]['directx'],
                self::PROPERTIES_SYS_MIN_SOUND_CARD => $data[BukaItem::TAG_SYS_MIN]['sound_card'],
                self::PROPERTIES_SYS_MIN_HDD => $data[BukaItem::TAG_SYS_MIN]['hdd'],
                self::PROPERTIES_SYS_MIN_INTERNET => $data[BukaItem::TAG_SYS_MIN]['internet'],
                self::PROPERTIES_SYS_MIN_ADDITIONALLY => $data[BukaItem::TAG_SYS_MIN]['additionally'],
                self::PROPERTIES_SYS_MIN_MAC_OS => $data[BukaItem::TAG_SYS_MIN_MAC]['os'],
                self::PROPERTIES_SYS_MIN_MAC_PROCESSOR => $data[BukaItem::TAG_SYS_MIN_MAC]['processor'],
                self::PROPERTIES_SYS_MIN_MAC_RAM => $data[BukaItem::TAG_SYS_MIN_MAC]['ram'],
                self::PROPERTIES_SYS_MIN_MAC_VIDEO_CARD => $data[BukaItem::TAG_SYS_MIN_MAC]['video_card'],
                self::PROPERTIES_SYS_MIN_MAC_DIRECTX => $data[BukaItem::TAG_SYS_MIN_MAC]['directx'],
                self::PROPERTIES_SYS_MIN_MAC_SOUND_CARD => $data[BukaItem::TAG_SYS_MIN_MAC]['sound_card'],
                self::PROPERTIES_SYS_MIN_MAC_HDD => $data[BukaItem::TAG_SYS_MIN_MAC]['hdd'],
                self::PROPERTIES_SYS_MIN_MAC_INTERNET => $data[BukaItem::TAG_SYS_MIN_MAC]['internet'],
                self::PROPERTIES_SYS_MIN_MAC_ADDITIONALLY => $data[BukaItem::TAG_SYS_MIN_MAC]['additionally'],
                self::PROPERTIES_SYS_REQ_OS => $data[BukaItem::TAG_SYS_REQ]['os'],
                self::PROPERTIES_SYS_REQ_PROCESSOR => $data[BukaItem::TAG_SYS_REQ]['processor'],
                self::PROPERTIES_SYS_REQ_RAM => $data[BukaItem::TAG_SYS_REQ]['ram'],
                self::PROPERTIES_SYS_REQ_DIRECTX => $data[BukaItem::TAG_SYS_REQ]['directx'],
                self::PROPERTIES_SYS_REQ_SOUND_CARD => $data[BukaItem::TAG_SYS_REQ]['sound_card'],
                self::PROPERTIES_SYS_REQ_VIDEO_CARD => $data[BukaItem::TAG_SYS_REQ]['video_card'],
                self::PROPERTIES_SYS_REQ_HDD => $data[BukaItem::TAG_SYS_REQ]['hdd'],
                self::PROPERTIES_SYS_REQ_INTERNET => $data[BukaItem::TAG_SYS_REQ]['internet'],
                self::PROPERTIES_SYS_REQ_ADDITIONALLY => $data[BukaItem::TAG_SYS_REQ]['additionally'],
                self::PROPERTIES_SYS_REQ_MAC_OS => $data[BukaItem::TAG_SYS_REQ_MAC]['os'],
                self::PROPERTIES_SYS_REQ_MAC_PROCESSOR => $data[BukaItem::TAG_SYS_REQ_MAC]['processor'],
                self::PROPERTIES_SYS_REQ_MAC_RAM => $data[BukaItem::TAG_SYS_REQ_MAC]['ram'],
                self::PROPERTIES_SYS_REQ_MAC_DIRECTX => $data[BukaItem::TAG_SYS_REQ_MAC]['directx'],
                self::PROPERTIES_SYS_REQ_MAC_SOUND_CARD => $data[BukaItem::TAG_SYS_REQ_MAC]['sound_card'],
                self::PROPERTIES_SYS_REQ_MAC_VIDEO_CARD => $data[BukaItem::TAG_SYS_REQ_MAC]['video_card'],
                self::PROPERTIES_SYS_REQ_MAC_HDD => $data[BukaItem::TAG_SYS_REQ_MAC]['hdd'],
                self::PROPERTIES_SYS_REQ_MAC_INTERNET => $data[BukaItem::TAG_SYS_REQ_MAC]['internet'],
                self::PROPERTIES_SYS_REQ_MAC_ADDITIONALLY => $data[BukaItem::TAG_SYS_REQ_MAC]['additionally'],
                self::PROPERTIES_ADDITION_INFORMATION_GENRE => $data[BukaItem::TAG_ADDITION_INFORMATION]['genre'],
                self::PROPERTIES_ADDITION_INFORMATION_DEVELOPER => $data[BukaItem::TAG_ADDITION_INFORMATION]['developer'],
                self::PROPERTIES_ADDITION_INFORMATION_PUBLISHER => $data[BukaItem::TAG_ADDITION_INFORMATION]['publisher'],
                self::PROPERTIES_ADDITION_INFORMATION_PUBLISHER_RUSSIAN => $data[BukaItem::TAG_ADDITION_INFORMATION]['publishers_russian'],
                self::PROPERTIES_ADDITION_INFORMATION_AGERATING => $data[BukaItem::TAG_ADDITION_INFORMATION]['agerating'],
                self::PROPERTIES_ADDITION_INFORMATION_LOCALIZATION => $data[BukaItem::TAG_ADDITION_INFORMATION]['localization'],
                self::PROPERTIES_ADDITION_INFORMATION_PLATFORM => $data[BukaItem::TAG_ADDITION_INFORMATION]['platforms'],//todo: check multiple?
                self::PROPERTIES_ADDITION_INFORMATION_SYSTEM_ACTIVATION => $data[BukaItem::TAG_ADDITION_INFORMATION]['system_activation'],
                self::PROPERTIES_ADDITION_INFORMATION_CATEGORY => $data[BukaItem::TAG_ADDITION_INFORMATION]['category'],
                self::PROPERTIES_ADDITION_INFORMATION_REGIONS => $data[BukaItem::TAG_ADDITION_INFORMATION]['regions'],
                self::PROPERTIES_IMAGES_MAIN => $imagesMain,
                self::PROPERTIES_IMAGES_SCREENSHOT => [],
                self::VIDEO_YOUTUBE => $data[BukaItem::TAG_VIDEOS],
            ],
        ];


        if ($data[self::PROPERTY_VALUES][self::PROPERTIES_DATETIME_REALASE]) {
            if (time() < strtotime($data[self::PROPERTY_VALUES][self::PROPERTIES_DATETIME_REALASE])) {
                array_push($data[self::IBLOCK_SECTION_ID], 128);
                $data[self::IBLOCK_SECTION_ID] = array_unique($data[self::IBLOCK_SECTION_ID]);
            }
        }


        /**
         * Проверяем элемент каталога, в случае его наличия
         * будет проведено сравнение полей для выявления расхождений и
         * обновления элемента.
         */
        $element = $this->getElement($data);

        if ($element[self::PREVIEW_PICTURE] === null) {//<-- Обновление превью
            /**
             * Получение обложки игры
             */
            $preview = $this->checkFile($images);
            if ($preview === false) {//<-- Если обложка не указана, пробуем запросить файл с типом main
                $imageMainId = (int)current($imagesMain);
                if ($imageMainId) {
                    $data[self::PREVIEW_PICTURE] = CFile::MakeFileArray($imageMainId);
                }
            }

            /**
             * Проверка превью, если не определено на предыдущих этапах, то берем первый из скринов
             */
            if (!$element['UPDATE_PROPERTIES'][self::PROPERTIES_IMAGES_SCREENSHOT] && $element[self::PREVIEW_PICTURE] === null) {
                if ($data[self::PROPERTY_VALUES][self::PROPERTIES_IMAGES_SCREENSHOT]) {
                    $element['UPDATE_PROPERTIES'][self::PROPERTIES_IMAGES_SCREENSHOT] = $data[self::PROPERTY_VALUES][self::PROPERTIES_IMAGES_SCREENSHOT];
                }
            }
        }

        /**
         * Скриншоты
         */
        $data[self::PROPERTY_VALUES][self::PROPERTIES_IMAGES_SCREENSHOT] = $this->getImagesScreenshot($images, $element);

        if ($element[self::IMAGE_750_470]['VALUE'] === null) {
            $additional750470 = $this->checkFile($images, 'additional750x470');
            if ($additional750470) {
                $element['UPDATE_PROPERTIES'][self::IMAGE_750_470] = $additional750470;
            }
        }
        if ($element[self::IMAGE_750_470]['VALUE'] === null) {
            $additional750580 = $this->checkFile($images, 'additional750x580');
            if ($additional750580) {
                $element['UPDATE_PROPERTIES'][self::IMAGE_750_580] = $additional750580;
            }
        }

        if (null === $element) {
            $result = $iBlockElement->Add($data, false, true);

            if (false == $result) {
                if (Api::debug) {
                    AddMessage2Log('Не удалось добавить элемент инфоблока: ' . $iBlockElement->LAST_ERROR, Api::module_id);
                }
            } else {
                if (Api::debug) {
                    AddMessage2Log('Добавлен элемент инфоблока #' . $result . ': ' . print_r($data, true), Api::module_id);
                }
                /**
                 * Добавляем элемент каталога
                 */
                $catalogProduct = new CCatalogProduct();

                $addCatalogProduct = $catalogProduct->Add([
                    'ID' => $result,
                    'AVAILABLE' => ((int)$data[self::PROPERTY_VALUES][self::PROPERTIES_AVAILABLE] == 0) ? 'N' : 'Y',
                    'PURCHASING_PRICE' => $data[self::PRICE_FIELD],
                    'QUANTITY_TRACE' => 'N',
                    'CAN_BUY_ZERO' => 'Y',
                    'QUANTITY' => 1000,
                ]);
                $element = [];
                self::checkDiscount($result, $data, $element);
                /**
                 * Создание скидок Битрикса
                 */
                if (!empty($element['CREATE_DISCOUNT'])) {
                    $discount = new CCatalogDiscount();
                    $idDiscount = $discount->Add($element['CREATE_DISCOUNT']);
                    if (!$idDiscount) {
                        $ex = $APPLICATION->GetException();
                        if (Api::debug) {
                            AddMessage2Log('Ошибка создания скидки:' . $ex->GetString(), Api::module_id);
                        }
                    } else {
                        if (Api::debug) {
                            AddMessage2Log('Для элемента каталога создана скидка #:' . $idDiscount, Api::module_id);
                        }
                    }
                }
                if (false === $addCatalogProduct) {
                    if (Api::debug) {
                        AddMessage2Log('Не удалось добавить элемент каталога.', Api::module_id);
                    }
                } else {
                    if (Api::debug) {
                        AddMessage2Log('Добавлен элемент каталога #' . $result, Api::module_id);
                    }
                    /**
                     * Добавляем цену
                     */
                    $addPrice = CPrice::Add([
                        'PRODUCT_ID' => $result,
                        'CATALOG_GROUP_ID' => self::CATALOG_GROUP_ID,
                        'CURRENCY' => 'RUB',
                        'PRICE' => $data[self::PROPERTY_VALUES][self::PRICE_FIELD],
                    ]);
                    if ($addPrice == false) {
                        if (Api::debug) {
                            AddMessage2Log('Не удалось добавить цену: ' . print_r($APPLICATION->GetException(), true), Api::module_id);
                        }
                    } else {
                        if (Api::debug) {
                            AddMessage2Log('Добавлена цена #' . $addPrice . ' для #' . $result, Api::module_id);
                        }
                    }
                }
            }
        } else {

            if ($element[self::PREVIEW_PICTURE] === null && $element[self::PROPERTIES_IMAGES_MAIN]) {
                $imageMainId = (int)current($element[self::PROPERTIES_IMAGES_MAIN]);
                if ($imageMainId) {
                    $element['UPDATE_FIELDS'][self::PREVIEW_PICTURE] = CFile::MakeFileArray($imageMainId);
                }
            }

            if (!empty($element['UPDATE_FIELDS']) || !empty($element['UPDATE_PROPERTIES'])) {
                if (count($element['UPDATE_FIELDS'])) {
                    $data = [];
                    foreach ($element['UPDATE_FIELDS'] as $name => $value) {
                        $data[$name] = $value;
                    }
                }
                if ($data['CODE'] !== $element['CODE']) {
                    $data['CODE'] = Cutil::translit($element['NAME'], "ru", $arParams);
                }
                if ($element['UPDATE_PROPERTIES'] !== null && count($element['UPDATE_PROPERTIES'])) {
                    foreach ($element['UPDATE_PROPERTIES'] as $key => $value) {
                        $iBlockElement->SetPropertyValueCode($element['ID'], $key, $value);
                    }
                }

                $result = $iBlockElement->Update($element['ID'], $data, false, false);

                if (Api::debug) {
                    if ($iBlockElement->LAST_ERROR) {
                        AddMessage2Log('Ошибка обновления элемента инфоблока: #' . $element['ID'] . ' ' . $iBlockElement->LAST_ERROR, Api::module_id);
                    }
                    if (false == $result) {
                        AddMessage2Log('Не удалось обновить элемент инфоблока: ' . $iBlockElement->LAST_ERROR, Api::module_id);
                    } else {
                        AddMessage2Log('Обновлен элемент инфолока #' . $element['ID'] . ': ' . print_r($data, true), Api::module_id);
                    }
                }
            }
            if (!empty($element['UPDATE_PRICE'])) {
                $updatePriceData = [
                    'PRICE' => $element['UPDATE_PRICE']['PRICE'],
                ];
                $updatePrice = CPrice::Update($element['UPDATE_PRICE']['ID'], $updatePriceData);

                if (false === $updatePrice) {
                    AddMessage2Log('Не удалось обновить цену: ' . print_r($APPLICATION->GetException(), true), Api::module_id);
                } else {
                    if (Api::debug) {
                        AddMessage2Log('Обновлена цена для #' . $element['ID'] . ': ' . print_r($updatePriceData, true), Api::module_id);
                    }
                }
            }

            /**
             * Создание скидок Битрикса
             */
            if (!empty($element['CREATE_DISCOUNT'])) {
                $discount = new CCatalogDiscount();
                $idDiscount = $discount->Add($element['CREATE_DISCOUNT']);
                if (!$idDiscount) {
                    $ex = $APPLICATION->GetException();
                    if (Api::debug) {
                        AddMessage2Log('Ошибка создания скидки:' . $ex->GetString(), Api::module_id);
                    }
                } else {
                    if (Api::debug) {
                        AddMessage2Log('Для элемента каталога создана скидка #:' . $idDiscount, Api::module_id);
                    }
//                    $iBlockElement->SetPropertyValueCode($element['ID'], self::PROPERTIES_HIT, [
//                        82,//<-- STOCK
//                    ]);

                    $iBlockElement->SetPropertyValues($element['ID'], $element['IBLOCK_ID'],
                        [82], self::PROPERTIES_PRICE_RETAIL_STOCK
                    );
                }
            }
            /**
             * Обновление скидок Битрикса
             */
            if (!empty($element['UPDATE_DISCOUNT'])) {
                $discount = new CCatalogDiscount();
                foreach ($element['UPDATE_DISCOUNT'] as $arItemUpdateData) {
                    $res = $discount->Update($arItemUpdateData['ID'], [
                        'VALUE' => $arItemUpdateData['VALUE'],
                    ]);
                    if (!$res) {
                        $ex = $APPLICATION->GetException();
                        if (Api::debug) {
                            AddMessage2Log('Ошибка обновления скидки:' . $ex->GetString(), Api::module_id);
                        }
                    } else {
                        if (Api::debug) {
                            AddMessage2Log('Обновлена скидка #:' . print_r($arItemUpdateData, true), Api::module_id);
                        }
                    }
                }
            }
            /**
             * Удаление скидок Битрикса
             */
            if (!empty($element['DELETE_DISCOUNT'])) {
                $discount = new CCatalogDiscount();
                foreach ($element['DELETE_DISCOUNT'] as $ID) {
                    $discount->Delete($ID);
                }
            }
        }
    }

    /**
     * Проверяет существование элемента инфоблока
     *
     * В полях UPDATE_FIELDS и UPDATE_PROPERTIES находятся значения которые
     * необходимо обновить
     *
     * @param $data
     * @return null
     */
    protected function getElement($data)
    {
        $arFields = null;
        if (!isset($data[self::PROPERTY_VALUES][self::PROPERTIES_EXTERNAL_ID])) {
            $data[self::PROPERTIES_EXTERNAL_ID] = $data[self::PROPERTY_VALUES][self::PROPERTIES_EXTERNAL_ID];
        }
        $filter = [
            self::IBLOCK_ID => $data[self::IBLOCK_ID],
            self::NAME => $data[self::NAME],
            'PROPERTY_' . self::PROPERTIES_EXTERNAL_ID => $data[self::PROPERTIES_EXTERNAL_ID],
        ];

        $res = CIBlockElement::GetList(['SORT' => 'ASC'], $filter, false, false, ['ID',
            self::IBLOCK_ID, self::NAME, 'CODE', self::PREVIEW_PICTURE, self::PREVIEW_TEXT]);

        $ob = $res->GetNextElement();

        if ($ob) {
            $arFields = $ob->GetFields();
            if (Api::debug) {
                AddMessage2Log('[' . __METHOD__ . '] element fields:' . print_r($arFields, true), 'api');
            }
            //Проверка полей элемента и формирование массива для обновления
            $checkFields = [self::NAME, 'CODE', /*self::PREVIEW_PICTURE,*/
                self::PREVIEW_TEXT];
            foreach ($checkFields as $checkField) {
                if ($arFields[$checkField] != $data[$checkField]) {
                    $arFields['UPDATE_FIELDS'][$checkField] = $data[$checkField];
                }
            }

            //Проверка свойств элемента и формирование массива для обновления
            $tmp = $ob->GetProperties();


            foreach ($tmp as $property) {
                switch ($property['CODE']) {
                    case 'HIT':
                        $arFields['PROPERTY_HIT'] = $property;
                        if ($property['VALUE_ENUM_ID']) {
                            if (in_array(IBlockCatalog::$id_stock, $property['VALUE_ENUM_ID'])) {
                                $arFields['CHECK_STOCK_LABEL'] = $property['VALUE_ENUM_ID'];
                            }
                        }
                        break;
                    case self::IMAGE_750_580:
                    case self::IMAGE_750_470:
                        $arFields[$property['CODE']] = $property;
                        break;
                }
                if (isset($data[self::PROPERTY_VALUES][$property['CODE']])) {
                    switch ($property['CODE']) {

                        case self::PROPERTIES_IMAGES_SCREENSHOT:
                            $arFields['CHECK_FILES'] = [];
                            $_tmp = [];//<-- Проверка дубликатов файлов
                            foreach ($property['VALUE'] as $id) {
                                $rsFile = CFile::GetByID($id);
                                $arFile = $rsFile->Fetch();
                                if (!in_array($arFile['ORIGINAL_NAME'], $_tmp)) {
                                    if ($arFile['ORIGINAL_NAME'] !== "" && null !== $arFile['ORIGINAL_NAME']) {
                                        $_tmp[$id] = $arFile['ORIGINAL_NAME'];
                                        $arFields['CHECK_FILES'][$id] = $arFile['ORIGINAL_NAME'];
                                    }
                                }
                            }
                            break;
                        case self::PROPERTIES_IMAGES_MAIN:
                            $arFields[self::PROPERTIES_IMAGES_MAIN] = $property['VALUE'];
                            break;
                        default:
                            if ($property['VALUE'] != $data[self::PROPERTY_VALUES][$property['CODE']]) {
                                $arFields['UPDATE_PROPERTIES'][$property['CODE']] = $data[self::PROPERTY_VALUES][$property['CODE']];
                            }
                            break;
                    }
                }
            }

            /**
             * Проверка скидок
             */
            self::checkDiscount($arFields['ID'], $data, $arFields);

            if ($arFields['CHECK_STOCK_LABEL']) {

                $idx = array_search(IBlockCatalog::$id_stock, $arFields['PROPERTY_HIT']['VALUE_ENUM_ID']);

                if ($idx !== false) {
                    unset($arFields['PROPERTY_HIT']['VALUE_ENUM_ID'][$idx]);
                    $arProperty = [
                        'HIT' => array_unique($arFields['PROPERTY_HIT']['VALUE_ENUM_ID']),
                    ];
                    if ($arProperty['HIT']) {
                        CIBlockElement::SetPropertyValuesEx($arFields['ID'], false, $arProperty);
                    }
                    if (Api::debug) {
                        AddMessage2Log('[' . __METHOD__ . '] Определена свойство HIT:' . print_r($arFields['DELETE_DISCOUNT'], true), 'api');
                    }
                    unset($arFields['CHECK_STOCK_LABEL']);
                }
            }

            /**
             * Получение цены
             */
            $dbRes = CPrice::GetList([],
                [
                    "PRODUCT_ID" => $arFields['ID'],
                    "CATALOG_GROUP_ID" => self::CATALOG_GROUP_ID,
                ]
            );

            if ($arPrice = $dbRes->Fetch()) {
                if ((float)$arPrice['PRICE'] !== (float)$data[self::PROPERTY_VALUES][self::PRICE_FIELD]) {
                    $arFields['UPDATE_PRICE'] = [
                        'ID' => $arPrice['ID'],
                        'PRICE' => $data[self::PROPERTY_VALUES][self::PRICE_FIELD],
                    ];
                }
            } else {
                if (Api::debug) {
                    AddMessage2Log('[' . __METHOD__ . '] price not found?:' . print_r($dbRes, true), 'api');
                }
            }


            $data[self::PROPERTY_VALUES][self::PROPERTIES_PRICE_RETAIL_STOCK] = (float)$data[self::PROPERTY_VALUES][self::PROPERTIES_PRICE_RETAIL_STOCK];

            if (Api::debug) {
                AddMessage2Log('[' . __METHOD__ . '] fields:' . print_r($arFields, true), 'api');
            }
        }


        return $arFields;
    }

    /**
     * Работа со скидками Битрикса
     *
     * Нужно проверить есть ли у товара скида,
     * если она есть и от АПИ не поступает информация о акционной цене,
     * скидку Битрикса нужно удалить, если наоборот то скидку нужно создать,
     * если скидка существует но суммы разные, скидку нужно обновить=)
     *
     * @param $ID
     * @param $data
     * @param $arFields
     */
    public static function checkDiscount($ID, $data, &$arFields)
    {
        $discount = new CCatalogDiscount();
        $dbProductDiscounts = $discount->GetList(// <-- Получаем активные скидки на товар
            ["SORT" => "ASC"],
            ["PRODUCT_ID" => $ID, "ACTIVE" => "Y",],
            false,
            false,
            [
                "ID", "SITE_ID", "ACTIVE", "ACTIVE_FROM", "ACTIVE_TO", "RENEWAL", "NAME",
                "SORT", "MAX_DISCOUNT", "VALUE_TYPE", "VALUE", "CURRENCY", "PRODUCT_ID",
            ]
        );
        if (Api::debug) {
            AddMessage2Log('[' . __METHOD__ . '] Проверка скидки для товара: #' . $ID . print_r($data, true), 'api');
        }
        if ($data[self::PROPERTY_VALUES][self::PROPERTIES_PRICE_RETAIL_STOCK] > 0) {
            if ($data[self::PROPERTY_VALUES][self::PRICE_FIELD] != $data[self::PROPERTY_VALUES][self::PROPERTIES_PRICE_RETAIL_STOCK]) {
                // сумма скидки
                $sumDiscount = round((float)($data[self::PROPERTY_VALUES][self::PRICE_FIELD] - $data[self::PROPERTY_VALUES][self::PROPERTIES_PRICE_RETAIL_STOCK]), 1);
                $arFields['UPDATE_DISCOUNT'] = [];
                if (Api::debug) {
                    AddMessage2Log('[' . __METHOD__ . '] Определена скидка для товара:' . print_r([
                            'PRODUCT_ID' => $ID,
                            self::PROPERTIES_PRICE_RETAIL_STOCK => $data[self::PROPERTY_VALUES][self::PROPERTIES_PRICE_RETAIL_STOCK],
                            self::PRICE_FIELD => $data[self::PROPERTY_VALUES][self::PRICE_FIELD],
                            'sumDiscount' => $sumDiscount,
                        ], true), 'api');
                }
                $create = true;
                // Есть скидки на товар в Битриксе
                while ($arProductDiscounts = $dbProductDiscounts->Fetch()) {// Если скидки есть...
                    $arProductDiscounts['VALUE'] = round((float)$arProductDiscounts['VALUE'], 1);
                    if (Api::debug) {
                        AddMessage2Log('[' . __METHOD__ . '] Найдена скидка Битрикс:' . print_r([
                                'ID' => $arProductDiscounts['ID'],
                                'VALUE' => $sumDiscount,
                                'OLD_VALUE' => (float)$arProductDiscounts['VALUE'],
                            ], true), 'api');
                    }
                    switch ($arProductDiscounts['VALUE_TYPE']) {
                        case 'F':
                            $create = false;
                            if (!$arFields['PROPERTY_HIT']['VALUE_ENUM_ID']) {
                                $arFields['PROPERTY_HIT']['VALUE_ENUM_ID'] = [];
                            }
                            array_push($arFields['PROPERTY_HIT']['VALUE_ENUM_ID'], IBlockCatalog::$id_stock);
                            $arProperty = [
                                "HIT" => array_unique($arFields['PROPERTY_HIT']['VALUE_ENUM_ID']),
                            ];

                            if ($arProperty) {
                                CIBlockElement::SetPropertyValuesEx($ID, false, $arProperty);
                            }
                            if ($sumDiscount != (float)$arProductDiscounts['VALUE']) {//... проверяем сумму скидки, если есть расхождения, обновляем скидку
                                array_push($arFields['UPDATE_DISCOUNT'], [
                                    'ID' => $arProductDiscounts['ID'],
                                    'VALUE' => $sumDiscount,
                                    'OLD_VALUE' => (float)$arProductDiscounts['VALUE'],
                                ]);
                                if (Api::debug) {
                                    AddMessage2Log('[' . __METHOD__ . '] Определена скидка для обновления:' . print_r($arFields['UPDATE_DISCOUNT'], true), 'api');
                                }
                            }
                            break;
                    }
                }

                if (count($arFields['UPDATE_DISCOUNT']) == 0 && $create === true) {
                    $arFields['CREATE_DISCOUNT'] = [
                        'SITE_ID' => 's1',
                        'ACTIVE' => 'Y',
                        'NAME' => 'Автоматическая скида, создана в АПИ Бука для товара #' . $ID,
                        'VALUE_TYPE' => 'F',//<-- фиксированная сумма скидки, возможно так же проверить S для установки новой цены...
                        'VALUE' => $sumDiscount,
                        'CURRENCY' => 'RUB',
                        'CONDITIONS' => [
                            'CLASS_ID' => 'CondGroup',
                            'DATA' => ['All' => 'AND', 'True' => 'True',],
                            'CHILDREN' => [
                                [
                                    'CLASS_ID' => 'CondIBElement',//<-- CondIBElement - товар;
                                    'DATA' => ['logic' => 'Equal', 'value' => $ID,],
                                ],
                            ],
                        ],
                    ];
                    if ($data['period_to']) {
                        $arFields['CREATE_DISCOUNT']['ACTIVE_FROM'] = DateTime::createFromPhp(new \DateTime());//date('d-m-Y H:i:s');
                        $arFields['CREATE_DISCOUNT']['ACTIVE_TO'] = DateTime::createFromPhp($data['period_to']);

                    }
                    if (Api::debug) {
                        AddMessage2Log('[' . __METHOD__ . '] Определена скидка для создания:' . print_r($arFields['CREATE_DISCOUNT'], true), 'api');
                    }
                }
            }
        } else {
            $arFields['DELETE_DISCOUNT'] = [];
            while ($arProductDiscounts = $dbProductDiscounts->Fetch()) {// Если скидки есть - их нужно удалить!!!
                array_push($arFields['DELETE_DISCOUNT'], $arProductDiscounts['ID']);
            }
            if ($arFields['CHECK_STOCK_LABEL']) {
                $idx = array_search(IBlockCatalog::$id_stock, $arFields['PROPERTY_HIT']['VALUE_ENUM_ID']);

                if ($idx !== false) {
                    $arFields['PROPERTY_HIT']['VALUE_ENUM_ID'][$idx] = 0;
                    $arProperty = [
                        'HIT' => array_unique($arFields['PROPERTY_HIT']['VALUE_ENUM_ID']),
                    ];
                    CIBlockElement::SetPropertyValuesEx($ID, false, $arProperty);
                    if (Api::debug) {
                        AddMessage2Log('[' . __METHOD__ . '] Определена свойство HIT:' . print_r($arFields['DELETE_DISCOUNT'], true), 'api');
                    }
                    unset($arFields['CHECK_STOCK_LABEL']);
                }
            }

            if (Api::debug) {
                AddMessage2Log('[' . __METHOD__ . '] Определена скидка для удаления:' . print_r($arFields['DELETE_DISCOUNT'], true), 'api');
            }
        }
    }

    /**
     * @param $image
     * @return bool|mixed
     */
    protected function getFile($image)
    {
        if ($this->redirectCounter >= 5) {
            return false;
        }
        $image['src'] = str_replace('http:', 'https:', $image['src']);
        $content = $this->transport->get($image['src']);
        if (Api::debug) {
            AddMessage2Log('Curl: ' . print_r([
                    'src' => $image['src'],
                    //'content' => $content,
                    'httpStatusCode' => $this->transport->httpStatusCode,
                ], true), Api::module_id);
        }
        switch ($this->transport->httpStatusCode) {
            case 301:
            case 302:
                $this->redirectCounter++;
                AddMessage2Log('Curl redirect (' . $this->redirectCounter . '): ' . print_r([
                        'src' => $image['src'],
//                        'content' => $content,
                        'httpStatusCode' => $this->transport->httpStatusCode,
                    ], true), Api::module_id);
                $location = $this->transport->responseHeaders->offsetGet('location');
                if ($location) {
                    $content = $this->getFile(['src' => $location]);
                }
                break;
            case 200:
                return $content;
            default:
                if (Api::debug) {
                    AddMessage2Log('Curl: Ошибка получения файла: ' . print_r([
                            'src' => $image['src'],
                            //'content' => $content,
                            'httpStatusCode' => $this->transport->httpStatusCode,
                        ], true), Api::module_id);
                }
                break;
        }

        return null;
    }

    /**
     * Проверяет наличие файла
     *
     * @param $data
     * @param string $extType
     * @return array|bool|null
     */
    public function checkFile($data, $extType = 'cover')
    {
        $id = null;
        foreach ($data as $image) {
            switch ($image["type"]) {
                case $extType:
                    $strFileName = GetFileName($image["src"]);

                    if (Api::debug) {
                        AddMessage2Log('Определение файла ' . $extType . ': ' . $strFileName, Api::module_id);
                    }
                    $res = CFile::GetList(array("FILE_SIZE" => "desc"), array(
                        "MODULE_ID" => Api::module_id,
                        "ORIGINAL_NAME" => $strFileName,
                    ));
                    $res_arr = $res->GetNext();
                    if (!$res_arr) {
                        if (strpos($image['src'], 'http://partners.buka.ru/') === false) {
                            $image['src'] = 'http://partners.buka.ru/' . $image['src'];
                        }
                        if (Api::debug) {
                            AddMessage2Log('Файл не найден. Попытка получить файл  ' . $extType . ': ' . $image['src'], Api::module_id);
                        }
                        $content = $this->getFile($image);

                        if ($content !== false) {
                            $id = CFile::SaveFile([
                                'name' => $image['src'],
                                'content' => $content,
                                'type' => 'image/jpg',
                                'MODULE_ID' => Api::module_id,
                            ], $this->savePath);
                            if (Api::debug) {
                                AddMessage2Log('Файл  ' . $extType . ' получен и сохранен под ID: ' . $id, Api::module_id);
                            }
                        }
                    } else {
                        if (Api::debug) {
                            AddMessage2Log('Файл  ' . $extType . ' найден в базе данных: ' . print_r($res_arr, true), Api::module_id);
                        }
                        $id = $res_arr['ID'];
                    }
                    break;
                default:
                    break;
            }
        }

        return ($id) ? CFile::MakeFileArray($id) : false;
    }

    /**
     * Возвращает картинки
     *
     * @param $data
     * @return array
     */
    public function getImagesMain($data)
    {
        $result = [];
        foreach ($data as $image) {
            switch ($image["type"]) {
                case 'main':
                    $strFileName = GetFileName($image["src"]);
                    if (Api::debug) {
                        AddMessage2Log('Определение файла main: ' . $strFileName, Api::module_id);
                    }
                    $res = CFile::GetList(array("FILE_SIZE" => "desc"), array("MODULE_ID" => Api::module_id, "ORIGINAL_NAME" => $strFileName));
                    $res_arr = $res->GetNext();
                    if (!$res_arr) {
                        $check = parse_url($image['src']);
                        if (!$check['host']) {
                            $image['src'] = 'https://partners.buka.ru/' . $image['src'];
                        }
                        $content = $this->getFile($image);
                        if (Api::debug) {
                            AddMessage2Log('Файл не найден. Попытка получить файл main: ' . $image['src'], Api::module_id);
                        }
                        if ($content !== false) {
                            $id = CFile::SaveFile([
                                'name' => $image['src'],
                                'content' => $content,
                                'type' => 'image/jpg',
                                'MODULE_ID' => Api::module_id,
                            ], $this->savePath);
                            if (Api::debug) {
                                AddMessage2Log('Файл main получен и сохранен под ID: ' . $id, Api::module_id);
                            }
                            array_push($result, $id);

                        } else {
                            if (Api::debug) {
                                AddMessage2Log('Не удалось загрузить файл main: ' . print_r([
                                        'src' => $image['src'],
                                        'content' => $content,
                                        'transport' => $this->transport,
                                    ]), Api::module_id);
                            }
                            //array_push($result, $this->no_photo_image);
                        }
                    } else {
                        if (Api::debug) {
                            AddMessage2Log('Файл main найден в базе данных: ' . print_r($res_arr, true), Api::module_id);
                        }
                        //array_push($result, $res_arr['ID']);
                    }
                    break;
                default:
                    break;
            }
        }
        $result = array_unique($result);

        return $result;
    }

    /**
     * Возвращает скриншоты
     *
     * @param $data
     * @param $element
     * @return array
     */
    public function getImagesScreenshot($data, $element)
    {
        $result = [];
        if (!$element['CHECK_FILES']) {
            $element['CHECK_FILES'] = [];
        }

        foreach ($data as $image) {
            switch ($image["type"]) {
                case 'screenshot':
                    $strFileName = GetFileName($image["src"]);

                    if (in_array($strFileName, $element['CHECK_FILES']) == false) {
                        if (Api::debug) {
                            AddMessage2Log('Определение файла screenshot: ' . print_r([
                                    "ORIGINAL_NAME" => $strFileName,
                                    'src' => $image["src"],
                                ], true), Api::module_id);
                        }
                        $res = CFile::GetList(array("FILE_SIZE" => "desc"), array(
                            "ORIGINAL_NAME" => $strFileName,
                            'MODULE_ID' => Api::module_id,
                        ));
                        $res_arr = $res->GetNext();
                        if (!$res_arr) {
                            if (strpos($image['src'], 'partners.buka.ru') === false) {
                                $image['src'] = 'https://partners.buka.ru/' . $image['src'];
                            }
                            $content = $this->getFile($image);

                            if (Api::debug) {
                                AddMessage2Log('Файл не найден. Попытка загрузить файл screenshot: '
                                    . print_r([
                                        'src' => $image['src'],
                                        'result' => ($content === false),
                                    ], true), Api::module_id);
                            }
                            if ($content !== false) {
                                $id = CFile::SaveFile([
                                    'name' => $image['src'],
                                    'content' => $content,
                                    'type' => 'image/jpg',
                                    'MODULE_ID' => Api::module_id,
                                ], $this->savePath);

                                if (Api::debug) {
                                    AddMessage2Log('Файл screenshot получен и сохранен под ID: ' . $id, Api::module_id);
                                }
                                array_push($result, $id);
                            } else {
                                if (Api::debug) {
                                    AddMessage2Log('Не удалось загрузить файл screenshot: ' . print_r([
                                            'src' => $image['src'],
                                            'content' => $content,
                                            'transport' => $this->transport,
                                        ], true), Api::module_id);
                                }
                            }
                        } else {
                            if (Api::debug) {
                                AddMessage2Log('Файл screenshot найден в базе данных: ' . print_r($res_arr, true), Api::module_id);
                            }
                            array_push($result, $res_arr['ID']);
                            if (Api::debug) {
                                AddMessage2Log('Файл screenshot добавлен к скриншотам: ' . print_r([
                                        'id' => $res_arr['ID'],
                                        'result' => $result,
                                    ], true), Api::module_id);
                            }
                        }
                    } else {
                        if (Api::debug) {
                            AddMessage2Log('Файл screenshot найден в массиве проверочных файлов: ' . print_r([
                                    'strFileName' => $strFileName,
                                    'CHECK_FILES' => $element['CHECK_FILES'],
                                ], true), Api::module_id);
                        }
                    }
                    break;
                default:
                    break;
            }
        }
        $result = array_unique($result);

        return $result;
    }

    /**
     * Обновление доступного кол-ва товара
     *
     * @param $data
     */
    public static function updateAvailable($data)
    {
        $filter = [
            self::IBLOCK_ID => $data[self::IBLOCK_ID],
            'PROPERTY_' . self::PROPERTIES_EXTERNAL_ID => $data[self::PROPERTIES_EXTERNAL_ID],
        ];

        if ((int)$data[self::PROPERTIES_EXTERNAL_ID] > 0) {
            if (Api::debug) {
                AddMessage2Log('[IBlockElement@updateAvailable] Поиск элемента каталога по фильтру:' . print_r($filter, true), Api::module_id);
            }
            $res = CIBlockElement::GetList([], $filter, false, false, ['ID', self::IBLOCK_ID, 'NAME', 'CODE']);
            $ob = $res->GetNextElement();
            if ($ob) {
                $iBlockElement = new CIBlockElement();
                $arFields = $ob->GetFields();
                if (Api::debug) {
                    AddMessage2Log('[IBlockElement@updateAvailable] Найден элемент каталога:' . print_r($arFields, true), Api::module_id);
                }
                $iBlockElement->SetPropertyValueCode($arFields['ID'], self::PROPERTIES_AVAILABLE, $data[self::PROPERTIES_AVAILABLE]);

                if (Api::debug) {
                    AddMessage2Log('[IBlockElement@updatePrice] Обновление свойств элемента каталога #' . $arFields['ID'] . ':' .
                        print_r($data, true), Api::module_id);
                }
            } else {
                if (Api::debug) {
                    AddMessage2Log('[IBlockElement@updateAvailable] Элемента каталога НЕ найден по фильтру:' . print_r($filter, true), Api::module_id);
                }
            }
        } else {
            if (Api::debug) {
                AddMessage2Log('[IBlockElement@updateAvailable] Элемента каталога НЕ найден по фильтру:' . print_r($filter, true), Api::module_id);
            }
        }
    }

    /**
     * Обновление акционных цен
     *
     * @param $data
     * @return bool
     */
    public static function updatePrice($data)
    {
        global $APPLICATION;
        $filter = [
            self::IBLOCK_ID => $data[self::IBLOCK_ID],
            'PROPERTY_' . self::PROPERTIES_EXTERNAL_ID => $data[self::PROPERTIES_EXTERNAL_ID],
        ];

        if (Api::debug) {
            AddMessage2Log('[IBlockElement@updatePrice] Поиск элемента каталога по фильтру:' . print_r($filter, true), Api::module_id);
        }
        $res = CIBlockElement::GetList([], $filter, false, false, ['ID', self::IBLOCK_ID]);
        $ob = $res->GetNextElement();
        if ($ob) {
            $iBlockElement = new CIBlockElement();
            $arFields = $ob->GetFields();
            if (Api::debug) {
                AddMessage2Log('[IBlockElement@updatePrice] Найден элемент каталога:' . print_r($arFields, true), Api::module_id);
            }
            /**
             * Получение цены
             */
            $dbRes = CPrice::GetList([],
                [
                    "PRODUCT_ID" => $arFields['ID'],
                    "CATALOG_GROUP_ID" => self::CATALOG_GROUP_ID,
                ]
            );

            $updateData = [
                self::PROPERTY_VALUES => [//TODO: Добавить все парамтеры ЦЕН из АПИ
                    self::PRICE_FIELD => 0,
                    self::PROPERTIES_PRICE_RETAIL_STOCK => $data[self::PROPERTIES_PRICE_RETAIL_STOCK],
                    self::PROPERTIES_PRICE_WHOLESALE_STOCK => $data[self::PROPERTIES_PRICE_WHOLESALE_STOCK],
                ],
            ];
            if ($arPrice = $dbRes->Fetch()) {
                if (Api::debug) {
                    AddMessage2Log('Получена цена товара #' . $arFields['ID'] . print_r($arPrice, true), Api::module_id);
                }
                $updateData[self::PROPERTY_VALUES][self::PRICE_FIELD] = $arPrice['PRICE'];
                $data[self::PRICE_FIELD] = $arPrice['PRICE'];
                $data[self::PROPERTY_VALUES] = $updateData[self::PROPERTY_VALUES];//<-- Для корректной работы обновления скидок Битрикс
            }
            $iBlockElement->SetPropertyValueCode($arFields['ID'], self::PROPERTIES_PRICE_RETAIL_STOCK, $data[self::PROPERTIES_PRICE_RETAIL_STOCK]);
//            $iBlockElement->SetPropertyValues($arFields['ID'], $arFields['IBLOCK_ID'],
//                [
//                    self::PROPERTIES_PRICE_RETAIL_STOCK => $data[self::PROPERTIES_PRICE_RETAIL_STOCK],
//                ]
//            );
            $iBlockElement->SetPropertyValueCode($arFields['ID'], self::PROPERTIES_PRICE_WHOLESALE_STOCK, $data[self::PROPERTIES_PRICE_WHOLESALE_STOCK]);
//            $iBlockElement->SetPropertyValues($arFields['ID'], $arFields['IBLOCK_ID'],
//                [
//                    self::PROPERTIES_PRICE_WHOLESALE_STOCK => $data[self::PROPERTIES_PRICE_WHOLESALE_STOCK],
//                ]
//            );

            /**
             * Проверка скидок для Битрикса!!!
             */
            self::checkDiscount($arFields['ID'], $data, $updateData);

            if (Api::debug) {
                AddMessage2Log('Обновлена скидка ???:' . print_r($updateData, true), Api::module_id);
            }
            /**
             * Создание скидок Битрикса
             */
            if (!empty($updateData['CREATE_DISCOUNT'])) {
                $discount = new CCatalogDiscount();
                $idDiscount = $discount->Add($updateData['CREATE_DISCOUNT']);
                if (!$idDiscount) {
                    $ex = $APPLICATION->GetException();
                    if (Api::debug) {
                        AddMessage2Log('Ошибка создания скидки:' . $ex->GetString(), Api::module_id);
                    }
                } else {
                    if (Api::debug) {
                        AddMessage2Log('Для элемента каталога создана скидка #:' . $idDiscount, Api::module_id);
                    }
                }
            }
            /**
             * Обновление скидок Битрикса
             */
            if (!empty($updateData['UPDATE_DISCOUNT'])) {
                $discount = new CCatalogDiscount();
                foreach ($updateData['UPDATE_DISCOUNT'] as $arItemUpdateData) {
                    $res = $discount->Update($arItemUpdateData['ID'], [
                        'VALUE' => $arItemUpdateData['VALUE'],
                    ]);
                    if (!$res) {
                        $ex = $APPLICATION->GetException();
                        if (Api::debug) {
                            AddMessage2Log('Ошибка обновления скидки:' . $ex->GetString(), Api::module_id);
                        }
                    } else {
                        if (Api::debug) {
                            AddMessage2Log('Обновлена скидка #:' . print_r($arItemUpdateData, true), Api::module_id);
                        }
                    }
                }
            }
            /**
             * Удаление скидок Битрикса
             */
            if (!empty($updateData['DELETE_DISCOUNT'])) {
                $discount = new CCatalogDiscount();
                foreach ($updateData['DELETE_DISCOUNT'] as $ID) {
                    $discount->Delete($ID);
                }
            }

        }

        return false;
    }
}
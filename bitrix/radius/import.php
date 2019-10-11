<?php

$_SERVER['DOCUMENT_ROOT'] = dirname(__FILE__);

/**
 * Created by PhpStorm.
 * User: Dark_Ghost
 * Date: 26.05.14
 * Time: 10:54
 */
class Import
{
    /**
     * @var int
     */
    public $iblock_id = 9;
    /**
     * @var string
     */
    public $priceCode = '1';
    /**
     * @var string
     */
    public $currency = 'EUR';
    /**
     * @var array
     */
    public $productAttrStorage = array();
    /**
     * @var array
     */
    public $product = array();
    /**
     * @var array
     */
    public $productAttr = array();
    /**
     * @var array
     */
    public $categoryProductStorage = array();
    /**
     * @var array
     */
    public $productAttrType = array();
    /**
     * @var array
     */
    public $productProperties = array();
    /**
     * @var SimpleXMLElement
     */
    public $xml;
    /**
     * @var int
     */
    public $ttl = 0;
    /**
     * @var array
     */
    public $messages = array();
    /**
     * @var array
     */
    protected $data = array();
    /**
     * @var string
     */
    private $ftpUser = '';
    /**
     * @var string
     */
    private $ftpPass = '';
    /**
     * @var string
     */
    private $ftpHost = '';
    /**
     * @var string
     */
    private $ftpFolder = 'PHOTO_GASTRO_MSK';

    public function __construct($file)
    {
        global $APPLICATION;
        $APPLICATION->restartBuffer();
        $this->ttl = (60 * 60) * 2;
        ini_set('max_execution_time', 0);

        $file = $this->_getFile($file, true);

        libxml_use_internal_errors(true);
        $this->xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . '/tmp/radius_ru.xml');
        if ($this->xml) {
            $this->_setLocalVariable('TProductAttr', 'productAttr', 'paptid'); //сортировка по разделу
            $this->_setLocalVariable('TProductAttr', 'productProperties', 'paid'); //сортировка по ид
            $this->_setLocalVariable('TProductStorage', 'productAttrStorage', 'psprid');
            $this->_setLocalVariable('TCatStorage', 'categoryProductStorage', 'cscatid');
            $this->_setLocalVariable('TProductType', 'productAttrType', 'ptid');
            $this->_setLocalVariable('TProduct', 'product', 'prid');
        } else {
            echo "Ошибка загрузки XML\n";
            foreach (libxml_get_errors() as $error) {
                echo "\t", 'line:' . $error->line . ' ' . $error->message;
            }
            die();
        }

    }

    /**
     * @param $file
     * @param bool $local
     * @return array|bool
     */
    protected function _getFile($file, $local = true)
    {
        $local_file = $_SERVER['DOCUMENT_ROOT'] . '/tmp/' . $file;

        if (file_exists($local_file) && $local) {
            //echo 'find file: ' . $file . PHP_EOL;
            $this->messages['local_file'] += 1;
            $this->messages['_local_file'] = 'Файлов в кэше: ' . $this->messages['local_file'];
            $rsFile = CFile::MakeFileArray($local_file);
            return array(
                "name" => $rsFile["name"],
                "type" => $rsFile["type"],
                "tmp_name" => $rsFile["tmp_name"],
                "error" => 0,
                "size" => filesize($local_file),
                "MODULE_ID" => "iblock",
            );
        }
        $start = time();
        //echo 'get file on ftp: ' . $file . PHP_EOL;
        $conn_id = ftp_connect($this->ftpHost, '8021');
        $login_result = ftp_login($conn_id, $this->ftpUser, $this->ftpPass);
        $mode = ftp_pasv($conn_id, TRUE);
        $this->messages['get_file'] += 1;
        $this->messages['_get_file'] = 'Файлов получено: ' . $this->messages['get_file'];
        if (ftp_get($conn_id, $local_file, $this->ftpFolder . '/' . $file, FTP_BINARY)) {
            ftp_close($conn_id);
            if (false == $local) {
                return $local_file;
            }
            $rsFile = CFile::MakeFileArray($local_file);
            // echo 'download file: ' . $file . ' (' . gmdate("H:i:s", time() - $start) . ')' . PHP_EOL;
            return array(
                "name" => $rsFile["name"],
                "type" => $rsFile["type"],
                "tmp_name" => $rsFile["tmp_name"],
                "error" => 0,
                "size" => filesize($local_file),
                "MODULE_ID" => "iblock",
            );
        }
        ftp_close($conn_id);
        return false;

    }

    /**
     * @param $tableName
     * @param $localName
     * @param $sortKey
     */
    public function _setLocalVariable($tableName, $localName, $sortKey)
    {
        $obCache = new CPHPCache();

        if ($obCache->InitCache($this->ttl, $tableName . $sortKey)
        ) {
            $this->{$localName} = $obCache->GetVars();
            $this->messages[] = 'Из кэша установлена переменная импорта #' . $localName;
        } elseif ($obCache->StartDataCache()) {
            foreach ($this->xml->{'database'}->children() as $second_table) {
                $tbl_attr = $second_table->attributes();
                $second_table_key = (string)$tbl_attr['name'];
                switch ($second_table_key) {
                    case $tableName:
                        foreach ($second_table->children() as $table_child) {
                            $data = array();
                            foreach ($table_child->{'field'} as $field) {
                                $name = $field->attributes();
                                $name = strtolower((string)$name['name']);
                                $data[$name] = (string)$field;
                            }
                            $this->{$localName}[$data[$sortKey]][] = $data;
                        }
                        break;
                }
            }
            $this->messages[] = 'В кэш установлена переменная импорта #' . $localName;
            $obCache->EndDataCache($this->{$localName});
        }
    }

    public $_messages;

    /**
     *
     */
    public function importSection()
    {
        $this->_messages = array();
        ini_set('max_execution_time', 0);
        echo 'start ' . __METHOD__ . PHP_EOL;
        $start = time();
        $existSection = $this->_getAllSection();
        $count = 0;
        $checkSection = array();
        $IBLOCK_ID = $this->iblock_id;
        $xml = $this->xml;
        $result = array('add' => 0, 'error' => 0, 'update' => 0, 'delete' => 0, 'delete_error' => 0);
        $skipData = array('TConfig');
        //fix delete old cateroty
        foreach ($xml->{'database'}->children() as $table_data) {
            $tbl_attr = $table_data->attributes();
            $table_key = (string)$tbl_attr['name'];
            if (!in_array($table_key, $skipData)) {
                if ($table_data->count()) {
                    switch ($table_key) {
                        case 'TCatalogue':
                            foreach ($table_data->children() as $table_child) {
                                foreach ($table_child->{'field'} as $field) {
                                    $count++;
                                    $name = $field->attributes();
                                    $name = (string)$name['name'];
                                    $data[$name] = (string)$field;
                                }
                                $checkSection[$data['CatId']] = $data['CatId'];
                            }
                            break;
                    }
                }
            }
        }
        $this->_messages[] = 'Разделов в xml:' . count($checkSection);

        foreach ($checkSection as $external_id) {
            if (isset($existSection[$external_id])) {
                unset($existSection[$external_id]);
            }
        }
        $this->messages[] = 'Отмечено для удаления:' . count($existSection);
        if (count($existSection)) {
            foreach ($existSection as $external_id => $id) {
                if (CIBlockSection::Delete($id)) {
                    $result['delete']++;
                } else {
                    $result['delete_error']++;
                }
            }
        }
        //end fix
//$res = CIBlockSection::GetByID(7976);
//$ar_res = $res->GetNext();
//        var_dump($ar_res); die;
        if (count($xml->{'database'}->children())) {
            $count = 0;
            foreach ($xml->{'database'}->children() as $table_data) {
                $tbl_attr = $table_data->attributes();
                $table_key = (string)$tbl_attr['name'];

                if (!in_array($table_key, $skipData)) {
                    if ($table_data->count()) {
                        switch ($table_key) {
                            case 'TCatalogue':
                                foreach ($table_data->children() as $table_child) {
                                    foreach ($table_child->{'field'} as $field) {
                                        $name = $field->attributes();
                                        $name = (string)$name['name'];
                                        $data[$name] = (string)$field;
                                    }
                                    $bs = new CIBlockSection;
                                    $insertData = Array(
                                        'ACTIVE' => ($data['CatShow']) ? 'Y' : 'N',
                                        'IBLOCK_ID' => $this->iblock_id,
                                        'NAME' => $data['CatMenuName'],
                                        'SORT' => $data['CatPriority'],
                                        'DESCRIPTION' => $data['CatDescrTop'],
                                        'DESCRIPTION_TYPE' => 'html',
                                        'CODE' => $data['CatId']
                                    );
                                    $deleteFile = false;
                                    if ($data['CatPicMedium'] <> '') {
                                        $rsFile = $this->_getFile($data['CatPicMedium'], true);
                                        if ($rsFile) {
                                            $insertData['PICTURE'] = $rsFile;
                                        }
                                    } else {
                                        $rsFile = CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'] . '/upload/nophoto-left-catalog.jpg');
                                        $insertData['PICTURE'] = array(
                                            "name" => $rsFile["name"],
                                            "type" => $rsFile["type"],
                                            "tmp_name" => $rsFile["tmp_name"],
                                            "error" => 0,
                                            "size" => filesize($_SERVER['DOCUMENT_ROOT'] . '/upload/nophoto-left-catalog.jpg'),
                                            "MODULE_ID" => "iblock",
                                        );
                                    }
                                    if ($data['CatPicSmall'] <> '') {
                                        $rsFile = $this->_getFile($data['CatPicSmall'], true);
                                        if ($rsFile) {
                                            $insertData['DETAIL_PICTURE'] = $rsFile;
                                        }
                                    } else {
                                        $rsFile = CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'] . '/upload/nophoto-left-catalog.jpg');
                                        $insertData['DETAIL_PICTURE'] = array(
                                            "name" => $rsFile["name"],
                                            "type" => $rsFile["type"],
                                            "tmp_name" => $rsFile["tmp_name"],
                                            "error" => 0,
                                            "size" => filesize($_SERVER['DOCUMENT_ROOT'] . '/upload/nophoto-left-catalog.jpg'),
                                            "MODULE_ID" => "iblock",
                                        );
                                    }

                                    $insertData['IPROPERTY_TEMPLATES'] = array(
                                        'SECTION_META_TITLE' => $data['CatMetaTitle'] ? $data['CatMetaTitle'] : $data['CatName'],
                                        'SECTION_META_KEYWORDS' => $data['CatMetaKeywords'] ? $data['CatMetaKeywords'] : $data['CatName'],
                                        'SECTION_META_DESCRIPTION' => $data['CatMetaDescription'] ? $data['CatMetaDescription'] : $data['CatName']
                                    );

                                    if ($data['CatParentId']) {
                                        $parent = $this->_checkSection(array(
                                            'CODE' => $data['CatParentId'],
                                            'IBLOCK_ID' => $this->iblock_id,
                                        ));
                                        if ($parent['ID']) {
                                            $insertData['IBLOCK_SECTION_ID'] = $parent['ID'];
                                            if (!$check = $this->_checkSection(array(
                                                'CODE' => $insertData['CODE']
                                            ))
                                            ) {
                                                $lastId = $bs->Add($insertData, false, false);
                                                if ($lastId == false) {
                                                    var_dump(array(
                                                        'insert' => $insertData,
                                                        'parent' => $parent,
                                                        'LAST_ERROR' => $bs->LAST_ERROR
                                                    ));
                                                }
                                                $result['add']++;
                                            } else {


                                                $bs->Update($check['ID'], $insertData, false, false);
                                                $result['update']++;
                                            }
                                        } else {
                                            die();
                                            //TODO: error
                                            $result['error']++;
                                        }
                                    } else {
                                        if (!$check = $this->_checkSection(array(
                                            'CODE' => $insertData['CODE'],
                                            '?NAME' => $insertData['NAME'],
                                            'IBLOCK_ID' => $this->iblock_id,
                                        ))
                                        ) {
                                            $bs->Add($insertData, false, false);
                                            $result['add']++;
                                        } else {
                                            $bs->Update($check['ID'], $insertData, false, false);
                                            $result['update']++;
                                        }
                                    }
                                }
                                break;
                            default:
                                break;
                        }
                    }
                }
            }
        }
        CIBlockSection::Resort($this->iblock_id);
        $this->_messages[] = 'Добавлено разделов:' . $result['add'];
        $this->_messages[] = 'Обновлено разделов:' . $result['update'];
        $this->_messages[] = 'Удалено разделов:' . $result['delete'];
        $this->_messages[] = 'Удалено разделов с ошибками:' . $result['delete_error'];
        $this->_messages[] = 'Ошибок при создание разделов:' . $result['error'];
        echo implode($this->_messages, PHP_EOL) . PHP_EOL;
        echo 'end ' . __METHOD__ . ' (' . gmdate("H:i:s", time() - $start) . ')' . PHP_EOL;
        return $result;
    }

    /**
     * @return array
     */
    public function _getAllSection()
    {
        $result = array();
        $ob = CIBlockSection::GetList(array('id' => 'asc'), array(
            'IBLOCK_ID' => $this->iblock_id
        ), false, array('ID', 'CODE'), false);
        while ($arResult = $ob->NavNext()) {
            $result[$arResult['CODE']] = $arResult['ID'];
        }
        return $result;
    }

    /**
     * @param $filter
     * @return array
     */
    private function _checkSection($filter)
    {
        global $DB;
        $dbResource = $res = $DB->Query("SELECT * FROM b_iblock_section WHERE CODE='{$filter['CODE']}'");
        $result = $dbResource->NavNext();
        return $result;
    }

    /**
     * @return int
     */
    public function importProductProperty()
    {
        $start = time();
        echo 'start ' . __METHOD__ . PHP_EOL;
        $result = array('add' => 0, 'error' => 0, 'update' => 0);
        $returnCount = 0;
        // $this->deleteAllProductProperty();
        $allProperty = array(
            'IMPORT_CODE' => array(
                'CODE' => 'IMPORT_CODE',
                'NAME' => 'Артикул',
                'ACTIVE' => 'Y',
                'SORT' => 500,
                'SEARCHABLE' => 'Y',
                'SMART_FILTER' => 'N',
                'PROPERTY_TYPE' => 'S',
                'IBLOCK_ID' => $this->iblock_id,
            ),
            'IMPORT_STATUS' => array(
                'CODE' => 'IMPORT_STATUS',
                'NAME' => 'Состояние',
                'ACTIVE' => 'Y',
                'SORT' => 501,
                'SEARCHABLE' => 'Y',
                'SMART_FILTER' => 'N',
                'PROPERTY_TYPE' => 'S',
                'IBLOCK_ID' => $this->iblock_id,
            ), #торговая марка; серия; страна
            'IMPORT_TM' => array(
                'CODE' => 'IMPORT_TM',
                'NAME' => 'Бренд',
                'ACTIVE' => 'Y',
                'SORT' => 502,
                'SEARCHABLE' => 'Y',
                'SMART_FILTER' => 'Y',
                'PROPERTY_TYPE' => 'S',
                'IBLOCK_ID' => $this->iblock_id,
            ),
            'IMPORT_SERIES' => array(
                'CODE' => 'IMPORT_SERIES',
                'NAME' => 'Серия',
                'ACTIVE' => 'Y',
                'SORT' => 503,
                'SEARCHABLE' => 'Y',
                'SMART_FILTER' => 'Y',
                'PROPERTY_TYPE' => 'S',
                'IBLOCK_ID' => $this->iblock_id,
            ),
            'IMPORT_COUNTRY' => array(
                'CODE' => 'IMPORT_COUNTRY',
                'NAME' => 'Страна',
                'ACTIVE' => 'Y',
                'SORT' => 503,
                'SEARCHABLE' => 'Y',
                'SMART_FILTER' => 'Y',
                'PROPERTY_TYPE' => 'S',
                'IBLOCK_ID' => $this->iblock_id,
            ),
            'IMPORT_SECTIONS' => array(
                'CODE' => 'IMPORT_SECTIONS',
                'NAME' => 'Значения категории бренда в которой находится позиция',
                'ACTIVE' => 'Y',
                'SORT' => 0,
                'SEARCHABLE' => 'Y',
                'SMART_FILTER' => 'N',
                'PROPERTY_TYPE' => 'S',
                'IBLOCK_ID' => $this->iblock_id,
            ),
            'IMPORT_SECTIONS_ALL' => array(
                'CODE' => 'IMPORT_SECTIONS_ALL',
                'NAME' => 'Категории в которой находится позиция',
                'ACTIVE' => 'Y',
                'SORT' => 0,
                'SEARCHABLE' => 'Y',
                'SMART_FILTER' => 'N',
                'PROPERTY_TYPE' => 'S',
                'IBLOCK_ID' => $this->iblock_id,
            )
        );
        foreach ($allProperty as $key => $property) {
            $checkResult = CIBlockProperty::GetList(array('id' => 'asc'), array(
                'IBLOCK_ID' => $this->iblock_id,
                'CODE' => $key,
            ));

            if (!$arCheckProperty = $checkResult->GetNext()) {
                $iblockproperty = new CIBlockProperty;
                $PropertyID = $iblockproperty->Add($allProperty[$key]);
                if ($PropertyID) {
                    $result['add']++;
                }
            } elseif ($arCheckProperty['ID']) {
                $iblockproperty = new CIBlockProperty;
                if ($iblockproperty->Update($arCheckProperty['ID'], $allProperty[$key])) {
                    $result['update']++;
                }
            } else {
                $result['error']++;
            }
        }

        if ($this->productAttr) {
            foreach ($this->productAttr as $key => $properties) {
                foreach ($properties as $property) {
                    $code = $this->_getPropertyCode($property);
                    $name = mb_ucfirst(trim($property['paname']));
                    $type = $property['patype'] == 3 ? 'N' : 'S';
                    if ($property['patype'] == 1) {
                        $type = 'L';
                    }
                    $allProperty[$code] = array(
                        'CODE' => $code,
                        'NAME' => $name,
                        'SEARCHABLE' => 'Y',
                        'SMART_FILTER' => 'Y',
                        'ACTIVE' => ($property['padisplay'] == 1) ? 'Y' : 'N',
                        'SORT' => (int)$property['paprio'],
                        'PROPERTY_TYPE' => $type,
                        'IBLOCK_ID' => $this->iblock_id,
                    );
                    $checkResult = CIBlockProperty::GetList(array('id' => 'asc'), array(
                        'IBLOCK_ID' => $this->iblock_id,
                        'CODE' => $code,
                    ));
                    if (!$arCheckProperty = $checkResult->GetNext()) {
                        $iblockproperty = new CIBlockProperty;
                        $PropertyID = $iblockproperty->Add($allProperty[$code]);
                        if ($type == 'L') {
                            CIBlockPropertyEnum::Add(array(
                                'PROPERTY_ID' => $PropertyID,
                                'EXTERNAL_ID' => 0,
                                'VALUE' => 'нет'
                            ));
                            CIBlockPropertyEnum::Add(array(
                                'PROPERTY_ID' => $PropertyID,
                                'EXTERNAL_ID' => 1,
                                'VALUE' => 'да'
                            ));
                        }
                        if ($PropertyID) {
                            $result['add']++;
                        }
                    } elseif ($arCheckProperty['ID']) {
                        $iblockproperty = new CIBlockProperty;
                        if ($iblockproperty->Update($arCheckProperty['ID'], $allProperty[$code])) {
                            $result['update']++;
                            if ($type == 'L') {
                                $property_enums = CIBlockPropertyEnum::GetList(
                                    Array("SORT" => "ASC", "VALUE" => "ASC"),
                                    Array('PROPERTY_ID' => $arCheckProperty['ID'],)
                                );
                                $enum_exist = array();
                                while ($enum_fields = $property_enums->GetNext()) {
                                    array_push($enum_exist, $enum_fields["VALUE"]);
                                }

                                if (!in_array('нет', $enum_exist)) {
                                    CIBlockPropertyEnum::Add(array(
                                        'PROPERTY_ID' => $arCheckProperty['ID'],
                                        'EXTERNAL_ID' => 0,
                                        'VALUE' => 'нет'
                                    ));
                                }
                                if (!in_array('да', $enum_exist)) {
                                    CIBlockPropertyEnum::Add(array(
                                        'PROPERTY_ID' => $arCheckProperty['ID'],
                                        'EXTERNAL_ID' => 1,
                                        'VALUE' => 'да'
                                    ));
                                }
                            }
                        }
                    } else {
                        $result['error']++;
                    }
                }
            }
        }
        echo 'end ' . __METHOD__ . ' (' . gmdate("H:i:s", time() - $start) . ')' . PHP_EOL;
        return $result;
    }

    /**
     * @param $property
     * @return string
     */
    private function _getPropertyCode($property)
    {
        return 'IMPORT_' . md5($property['patype'] . mb_ucfirst(trim($property['paname'])));
    }

    public function checkElements()
    {
        $this->_messages = array();
        echo 'start ' . __METHOD__ . PHP_EOL;
        $start = time();
        $result = array('add' => 0, 'error' => 0, 'update' => 0, 'delete' => 0, 'error_delete' => 0);
        /**
         *  получаем все существующие элементы
         */
        $existElements = $this->getAllElementsExternalId();
        $this->messages[] = 'Всего позиций:' . count($existElements);
        $deleteElements = array();
        foreach ($this->categoryProductStorage as $code => $products) {
            foreach ($products as $product) {
                $_product = $this->_getProduct($product['csprid']);
                $_product = current($_product);
                if (isset($existElements[$_product['prid']])) {//EXTERNAL_ID
                    unset($existElements[$_product['prid']]);//удаляем то что есть в файле
                }
            }
        }
        /**
         *  элементы которые остались в массиве нужно удалить
         */
        $this->_messages[] = 'Позиций для удаления: ' . count($existElements);
        if (count($existElements)) {
            foreach ($existElements as $external_id => $element_id) {

                if (is_array($external_id)) {
                    foreach ($external_id as $id) {
                        if (true == CIBlockElement::Delete($id)) {
                            $result['delete']++;
                        } else {
                            $result['error_delete']++;
                        }
                    }
                } else {
                    if (CIBlockElement::Delete($element_id)) {
                        $result['delete']++;
                    } else {
                        $result['error_delete']++;
                    }
                }
            }
        }
        $this->_messages[] = 'Позиций удалено: ' . $result['delete'];
        $this->_messages[] = 'Позиций удалено с ошибками: ' . $result['error_delete'];
        echo implode($this->_messages, PHP_EOL) . PHP_EOL;
        echo 'end ' . __METHOD__ . ' (' . gmdate("H:i:s", time() - $start) . ')' . PHP_EOL;
    }

    /**
     * Метод возвращает массив существующих элементов,
     *
     * внешний_ид=>ид
     *
     * @param int $ttl
     * @return array
     */
    public function getAllElementsExternalId($ttl = 600)
    {
        $result = array();
        $ob = CIBlockElement::getList(array('id' => 'desc'), array(
            'IBLOCK_ID' => $this->iblock_id,
            'IBLOCK_TYPE' => 'catalog'
        ), false, false, array('ID', 'EXTERNAL_ID'));
        while ($arResult = $ob->GetNext()) {
            if (isset($arResult['EXTERNAL_ID']))
                $result[$arResult['EXTERNAL_ID']][] = $arResult['ID'];
        }
        return $result;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function _getProduct($id)
    {
        return $this->product[$id];
    }

    /**
     *
     */
    public function importProduct()
    {
        $this->_messages = array();
        echo 'start ' . __METHOD__ . PHP_EOL;
        $start = time();
        if ($this->categoryProductStorage) {
            $result = array('add' => 0, 'error' => 0, 'update' => 0, 'delete' => 0, 'error_delete' => 0);
            $allSections = array();

            $rsParent = CIBlockSection::GetList(
                array(),
                array('ID' => 6222, 'IBLOCK_ID' => $this->iblock_id)
            );
            $arSectionParent = $rsParent->GetNext();
            $sectionRes = CIBlockSection::GetList(
                array('LEFT_MARGIN' => 'ASC'),
                array(
                    'IBLOCK_ID' => $this->iblock_id,
                    '>LEFT_MARGIN' => $arSectionParent['LEFT_MARGIN'],
                    '<RIGHT_MARGIN' => $arSectionParent['RIGHT_MARGIN'],
                ), false, array('ID', 'NAME', 'CODE', 'DEPTH_LEVEL', 'SECTION_PAGE_URL')
            );
            while ($sectionAr = $sectionRes->NavNext()) {
                $allSections[$sectionAr['ID']] = $sectionAr;
            }

            $storageProductSortById = array();
            foreach ($this->categoryProductStorage as $code => $products) {
                $section = $this->_checkSection(array(
                    'CODE' => $code
                ));
                foreach ($products as $product) {
                    $_product = $this->_getProduct($product['csprid']);
                    $_product = current($_product);
                    $storageProductSortById[$_product['prid']][] = $section['ID'];
                }
            }
            $this->messages['counter_section'] = 0;

            /**
             * получаем все свойства товаров
             */
            $IBlockProperties = $this->_getIBlockProperties();

            /**
             *  перебор категорий товаров
             */
            $count = 0;
            $countSection = 0;
            $this->messages['find_section'] = 0;
            foreach ($this->categoryProductStorage as $code => $products) {
                $section = $this->_checkSection(array(
                    'CODE' => $code
                ));

                $this->messages['counter_section']++;
                /**
                 * начало импорта
                 */
                $this->messages['counter_position'] = 0;
                foreach ($products as $product) {

                    $element = new CIBlockElement;

                    $_product = $this->_getProduct($product['csprid']);
                    $_product = current($_product);
                    $parse = explode(';', $_product['prmetakeywords']);
                    $addArray = array(
                        'EXTERNAL_ID' => $_product['prid'],
                        'CODE' => $_product['prid'],
                        'IBLOCK_ID' => $this->iblock_id,
                        'IBLOCK_SECTION_ID' => $section['ID'],
                        'NAME' => $_product['prname'],
                        'ACTIVE' => ($_product['prstate']) ? 'Y' : 'N',
                        'DETAIL_TEXT' => $_product['prdescr'],
                        'SORT' => $product['csprio'],
                        'PROPERTY_VALUES' => array(
                            'IMPORT_CODE' => $_product['prcode'],
                            'IMPORT_STATUS' => $_product['prstatus'],
                            'IMPORT_TM' => (isset($parse[0]) && $parse[0] <> '') ? $parse[0] : '',
                            'IMPORT_SERIES' => (isset($parse[1]) && $parse[1] <> '') ? $parse[1] : '',
                            'IMPORT_COUNTRY' => (isset($parse[2]) && $parse[2] <> '') ? $parse[2] : '',
                        )
                    );
                    /**
                     * этот алгоритм был предложил А.Ерёмин, суть в поиске позиции в
                     * разделе с брендами и в зависимости от того где она там находится
                     * присваивать бренд и серию...
                     */
                    //проверяем категории позиции
                    if (isset($storageProductSortById[$product['csprid']])) {
                        $addArray['PROPERTY_VALUES']['IMPORT_SECTIONS_ALL'] =
                            implode(',', $storageProductSortById[$product['csprid']]);
                        foreach ($storageProductSortById[$_product['prid']] as $categoryId) {
                            //тупо перебором ищем раздел в поставщиках
                            if (isset($allSections[$categoryId])) {
                                //находим
                                //нужно получить навигационную цепочку для этого раздела,
                                // хз может позиция в сериях или глубже, по сути пох*й, нам нужен первый индекс массива,
                                // он и будет брендом........
                                $navRes = CIBlockSection::GetNavChain($this->iblock_id, $allSections[$categoryId]['ID']);

                                $sections = array();
                                while ($navAr = $navRes->GetNext()) {
                                    $sections[] = $navAr;
                                }
                                $this->messages['find_section']++;
                                //записываем серию/бренд в свойства позиции
                                $addArray['PROPERTY_VALUES']['IMPORT_SECTIONS'] = implode('#', array(
                                    'ID' => $sections[1]['ID'],
                                    'NAME' => $sections[1]['NAME'],
                                    'SECTION_PAGE_URL' => $sections[1]['SECTION_PAGE_URL'],
                                ));
                            }
                        }
                    }
                    /**
                     * обработка свойств позиции
                     */
                    $properties = $this->_getElementProperties($addArray['EXTERNAL_ID']);
                    if ($properties) {
                        foreach ($properties as $property) {
                            $attr = $this->_getElementPropertyAttr($property['pspaid']);
                            if (count($attr)) {
                                $propertyCode = $this->_getPropertyCode(current($attr));
                                $addArray['PROPERTY_VALUES'][$propertyCode] =
                                    ($attr['patype'] == 1) ? $property['psintval'] : $property['pstextval'];
                            }
                        }
                    }
                    if ($_product['prpicsmall'] <> '') {
                        if ($preview = $this->_getFile($_product['prpicsmall'])) {
                            $addArray['PREVIEW_PICTURE'] = $preview;
                        }
                    } else {
                        $rsFile = CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'] . '/upload/nophoto.jpg');
                        $addArray['PREVIEW_PICTURE'] = array(
                            "name" => $rsFile["name"],
                            "type" => $rsFile["type"],
                            "tmp_name" => $rsFile["tmp_name"],
                            "error" => 0,
                            "size" => filesize($_SERVER['DOCUMENT_ROOT'] . '/upload/nophoto.jpg'),
                            "MODULE_ID" => "iblock",
                        );
                    }
                    if ($_product['prpiclarge'] <> '') {
                        if ($detail = $this->_getFile($_product['prpiclarge'])) {
                            $addArray['DETAIL_PICTURE'] = $detail;
                        }
                    } else {
                        $rsFile = CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'] . '/upload/nophoto.jpg');
                        $addArray['DETAIL_PICTURE'] = array(
                            "name" => $rsFile["name"],
                            "type" => $rsFile["type"],
                            "tmp_name" => $rsFile["tmp_name"],
                            "error" => 0,
                            "size" => filesize($_SERVER['DOCUMENT_ROOT'] . '/upload/nophoto.jpg'),
                            "MODULE_ID" => "iblock",
                        );
                    }

                    $elementId = null;
                    if (!$check = $this->_getIBlockElement(array(
                        'EXTERNAL_ID' => $addArray['EXTERNAL_ID'],
                        'SECTION_ID' => $section['ID']
                    ))
                    ) {
                        $elementId = $element->Add($addArray, false, false, false);
                        if ($elementId) {
                            $result['add']++;
                        }
                    } elseif ($check['ID']) {
                        $elementId = $check['ID'];
                        if ($element->Update($check['ID'], $addArray, false, false, false, false)) {
                            $result['update']++;
                        }
                    }

                    if ($elementId) {
                        $catalogId = $this->_getCatalogProduct($elementId);
                        if (!$catalogId) {
                            CCatalogProduct::Add(array(
                                'ID' => $elementId,
                                'QUANTITY' => $_product['prstore'],
                                'CAN_BUY_ZERO' => 'Y'
                            ));
                        } elseif ($catalogId['ID']) {
                            CCatalogProduct::Update($catalogId['ID'], array(
                                'QUANTITY' => $_product['prstore']
                            ));
                        }
                        $elementPrice = $this->_getCatalogPrice($elementId);
                        if (!$elementPrice) {
                            global $APPLICATION;
                            CPrice::Add(array(
                                'PRODUCT_ID' => $elementId,
                                'CATALOG_GROUP_ID' => $this->priceCode,
                                'CURRENCY' => $this->currency,
                                'PRICE' => $_product['prprice']
                            ));
                        } elseif ($elementPrice['ID']) {
                            CPrice::Update($elementPrice['ID'], array(
                                'CATALOG_GROUP_ID' => $this->priceCode,
                                'CURRENCY' => $this->currency,
                                'PRICE' => $_product['prprice']
                            ));
                        }
                    }
                    $this->messages['counter_position']++;
                }
            }
        }
        $this->_messages[] = 'Обработано разделов: ' . $this->messages['counter_section'];
        $this->_messages[] = 'Обработано элементов: ' . $this->messages['counter_position'];
        $this->_messages[] = 'Добавлено элементов: ' . $result['add'];
        $this->_messages[] = 'Обновлено элементов: ' . $result['update'];
        $this->_messages[] = 'Найдено разделов (брендов): ' . $this->messages['find_section'];

        unset($this->messages['find_section']);
        unset($this->messages['counter_section']);
        unset($this->messages['counter_position']);
        echo implode($this->_messages, PHP_EOL) . PHP_EOL;
        echo 'end ' . __METHOD__ . ' (' . gmdate("H:i:s", time() - $start) . ')' . PHP_EOL;
    }

    /**
     * @return array
     */
    private function _getIBlockProperties()
    {
        $result = array();
        $properties = CIBlockProperty::GetList(array('id' => 'asc'), array('IBLOCK_ID' => $this->iblock_id));
        while ($prop_fields = $properties->GetNext()) {
            $result[$prop_fields['CODE']] = $prop_fields;
        }
        return $result;
    }

    /**
     * @param $id
     * @return bool
     */
    private function _getElementProperties($id)
    {
        if (array_key_exists($id, $this->productAttrStorage)) {
            return $this->productAttrStorage[$id];
        }
        return false;
    }

    private function _getElementPropertyAttr($id)
    {
        return $this->productProperties[$id];
    }

    /**
     * @param $filter
     * @return array
     */
    private function _getIBlockElement($filter)
    {
        $dbRes = CIBlockElement::GetList(array('id' => 'asc'), $filter, false, false,
            Array("ID", "TMP_ID", "ACTIVE", "CODE", "PREVIEW_PICTURE", "DETAIL_PICTURE"));
        return $dbRes->NavNext();
    }

    /**
     * @param $id
     * @return array
     */
    private function _getCatalogProduct($id)
    {
        $dbRes = CCatalogProduct::GetList(array(), array('ID' => $id));
        return $dbRes->NavNext();
    }

    /**
     * @param $id
     * @return array
     */
    private function _getCatalogPrice($id)
    {
        $dbRes = CPrice::GetList(array(),
            array('PRODUCT_ID' => $id, 'CATALOG_GROUP_ID' => $this->priceCode));
        return $dbRes->NavNext();
    }

    /**
     *
     */
    public function deleteAllProductProperty()
    {
        $properties = CIBlockProperty::GetList(array('id' => 'asc'), array('IBLOCK_ID' => $this->iblock_id));
        while ($prop_fields = $properties->GetNext()) {
            CIBlockProperty::Delete($prop_fields['ID']);
        }
    }

    public function deleteAllSection()
    {
        $section = $this->_getAllSection();
        foreach ($section as $id) {
            //  CIBlockSection::Delete($id, false);
        }

    }
}

define('NO_AGENT_CHECK', true);
define('NO_KEEP_STATISTIC', true);
define('STOP_STATISTICS', true);
define('NOT_CHECK_PERMISSIONS', true);
define('PERFMON_STOP', true);

header('Content-Type: text/html; charset=UTF-8');

ini_set('display_errors', true);
ini_set('html_errors', true);
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL ^ E_NOTICE);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

if (class_exists('CModule')) {
    CModule::IncludeModule("iblock");
    CModule::IncludeModule("catalog");
}


mb_internal_encoding("UTF-8");
function mb_ucfirst($text)
{
    return mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
}

if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/tmp/radius_ru.xml')) {
    //unlink($_SERVER['DOCUMENT_ROOT'] . '/tmp/radius_ru.xml');
    //echo 'old file deleted...' . PHP_EOL;
}
//unlink($_SERVER['DOCUMENT_ROOT'] . '/tmp/radius_ru.xml');
$import = new Import('radius_ru.xml');
$import->importProductProperty();
$import->importSection();
$import->checkElements();
$import->importProduct();


die();

$start = time();
$action = (isset($_REQUEST['action'])) ? $_REQUEST['action'] : 'init';

?>
    <html>
    <head>
        <title>Импорт</title>
    </head>
    <body>
    <div>
        <p>Время жизни кэша: <?= $import->ttl ?></p>
        <? if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/tmp/radius_ru.xml')): ?>
            <p>Файл импорта найден в временной директории</p>
        <? else: ?>
            <p>Файл импорта не найден в временной директории</p>
        <? endif; ?>
        <p>-------------------------------------</p>
        <?
        unset($import->messages['local_file']);
        unset($import->messages['get_file']);


        $nextAction = 'property';
        $finish = false;
        switch ($action) {
            case 'property':
                $nextAction = 'section';
                $import->importProductProperty();
                break;
            case 'section':
                $import->importSection();
                $nextAction = 'check';
                break;
            case 'check':
                $import->checkElements();
                $nextAction = 'product';
                break;
            case 'product':
                $import->importProduct();
                $nextAction = 'finish';
                break;
            case 'init':
                $nextAction = 'property';
                break;
            case 'finish':
                $nextAction = null;
                $finish = true;
                if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/tmp/radius_ru.xml')) {
                    unlink($_SERVER['DOCUMENT_ROOT'] . '/tmp/radius_ru.xml');
                }
                break;
        }
        $import->messages[] = 'Затрачено времени: ' . gmdate("H:i:s", time() - $start);
        foreach ($import->messages as $message) {
            echo '<p>' . $message . '</p>';
        }
        ?>
    </div>
    <? if ($finish): ?>
        <h1>Импорт завершен</h1>
    <? endif; ?>
    <? if ($nextAction !== null): ?>
        <div id="counter"></div>
        <script type="text/javascript">
            var counter = 0,
                interval = setInterval(function () {
                    if (counter == 6000) {
                        clearInterval(interval);
                        window.location.href = '/import.php?action=<?= $nextAction ?>'
                    }
                    counter++;
                    document.getElementById('counter').innerHTML = counter;
                }, 1000)
        </script>
        <a href="/import.php?action=<?= $nextAction ?>">вперед к приключениям))</a>
    <? endif; ?>
    </body>
    </html>

<?
//unlink($_SERVER['DOCUMENT_ROOT'] . '/tmp/radius_ru.xml');

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");
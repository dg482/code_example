<?php

/**
 * Импорт товаров с 1С
 *
 * Class ImportNewCommand
 */
class ImportNewCommand extends CConsoleCommand
{

    /**
     * Список товаров
     * @var array
     */
    protected $product_list = [];

    /**
     * Список сопутствующих товаров
     * @var array
     */
    protected $product_offer_list = [];

    /**
     * Список параметров
     * @var array
     */
    protected $product_parameter_list = [];

    /**
     * Список рекомендуемых товаров для отображения в слайдере по категориям
     *
     * @var array
     */
    protected $product_recommended_list = [];


    public function actionIndex()
    {
        if (!ini_get('date.timezone')) {
            date_default_timezone_set('GMT');
        }
        error_reporting(E_ALL & ~E_NOTICE | E_STRICT);

        $root_path = dirname(dirname(dirname(__FILE__) . '../') . '../');

        $file_import = $this->checkFile();
        $import = simplexml_load_string($this->cleanXml($file_import));
        $report = [
            'total_elements' => 0,
            'total_deactivate' => 0,
            'total_activated' => 0,
            'total_exists_elements' => 0,
            'total_updated' => 0,
            'total_updated_parameters' => 0,
            'total_created' => 0,
            'total_parameter_created' => 0,
            'total_parameter_update' => 0,
            'element_deactivate' => [],
            'element_source_deactivate' => [],
            'element_created' => [],
            'total_updated_source' => 0,
            'total_find_element' => 0,
            'total_find_parameter' => 0,
            'element_activated' => [],
            'total_activated_sources' => 0
        ];

        $data = [
            'check' => [],// то что есть в б.д. продукции
            'check_parameters' => [],// то что есть в б.д. доп. товаров
            'not_exists' => [],// не существующие
            'update_elements' => [],
            'update_parameters' => [],
            'create_elements' => [],
            'create_parameters' => [],
            'create_recommended' => [],
            'all_code' => [],// все элементы в выгрузки по коду
            'all_exist_code' => [],// все элементы в б.д. по коду
            'element_deactivate' => [],// то что нужно отключить в б.д.,
            'element_activated' => [],//то что нужно включить
            'groups' => []
        ];
        $allProducts = ProductImport::model()->findAll();
        $allDopProducts = ProductParamImport::model()->findAll();
        $allProductsSource = ProductSource::model()->findAll();

        foreach ($allProducts as $product) {
            $data['check'][$product->code][] = $product;
            $data['all_exist_code'][$product->code] = $product->code;
        }
        foreach ($allDopProducts as $product) {
            $data['check_parameters'][$product->sku][] = $product;
        }
        foreach ($allProductsSource as $product) {
            $data['all_exist_code'][$product->code] = $product->code;
        }
        //очистка памяти
        unset($allProductsSource, $allProducts, $allDopProducts);
        //удаление файла
        unlink($file_import);
        foreach ($import->{'Каталог'}->{'Группа'} as $item) {
            $attributes = $item->attributes();
            $data['groups'][trim((string)$attributes['Идентификатор'])] = trim((string)$attributes['Наименование']);
        }

        $count = 0;
        foreach ($import->{'Каталог'}->{'Товар'} as $item) {
            $report['total_elements']++;
            $attributes = $item->attributes();
            $id = trim((string)$attributes['Идентификатор']);
            $sku = $this->getItemPropertiesAttributesValue($item, 'Артикул');
            $code = trim((string)$attributes['ИдентификаторВКаталоге']);
            $availability = trim((string)$attributes['Наличие']);
            $weight = $this->getItemPropertiesAttributesValue($item, 'Вес', 0);
            $weight = str_replace(array(',', " "), array('.', ''), $weight);
            $weight = preg_replace("/[^0-9.]/", '', $weight);
            $data['all_code'][$code] = $code;

            $parent = trim((string)$attributes['Родитель']);

            $element = [
                'id_1c' => $id,
                'code' => $code,
                'sku' => $sku <> '' ? $sku : $code,
                'title' => trim((string)$attributes['Наименование']),
                'pallet' => $this->_getPallete(['title_1c' => (string)$attributes['Наименование']]),//Паллет
                'price' => $this->_getPrice($item),//Цена
                'currency' => $this->_getCurrency($item),//Валюта
                'title_1c' => $this->getItemPropertiesAttributesValue($item, 'ПолноеНаименование'),
                'ordered' => $this->getItemPropertiesAttributesValue($item, 'Заказной'),
                'date_created' => $this->getItemPropertiesAttributesValue($item, 'ДатаСоздания'),
                'amount' => (int)$this->getItemPropertiesAttributesValue($item, 'Количество', 0),
                'country' => (int)$this->getItemPropertiesAttributesValue($item, 'СтранаПроисхождения', ''),
                'manufacturer' => $this->getItemPropertiesAttributesValue($item, 'Производитель', ''),
                'total_amount' => (int)$this->getItemPropertiesAttributesValue($item, 'ОбщееКоличество', 0),
                'multiplicity' => (int)$this->getItemPropertiesAttributesValue($item, 'КратностьЗаказа', 0),
                'popular'/*'amount_sales'*/ => (int)$this->getItemPropertiesAttributesValue($item, 'КоличествоПродаж', 0),
                'sum_sales' => (int)$this->getItemPropertiesAttributesValue($item, 'СуммаПродаж', 0),
                'analog' => (int)$this->getItemPropertiesAttributesValue($item, 'Аналог', 0),
                'availability' => (int)$this->getItemPropertiesAttributesValue($item, 'Наличие', 0),
                'images' => [],
                'weight' => $weight,
                'parent' => (isset($data['groups'][$parent])) ? $data['groups'][$parent] : ''
            ];

            if ($element['date_created']) {
                $element['date_created'] = strtotime(str_replace('/', '.', $element['date_created']));
                if ($element['date_created']) {
                    $element['date_created'] = date('Y-m-d H:i:s', $element['date_created']);
                }
            }
            if ($element['date_created'] == '') {
                unset($element['date_created']);
            }
            $description = $item->{'Описание'};
            if (isset($description[0]) && $description[0] <> '') {
                $element['description'] = (string)$description[0];
                $count++;
            } else {
                $element['description'] = '';
            }
            if ($item->{'Изображения'}->{'Изображение'} instanceof SimpleXMLElement) {
                foreach ($item->{'Изображения'}->{'Изображение'} as $image) {
                    array_push($element['images'], (string)$image);
                }
            }
            if ($item->{'Комплектующие'}->{'Комплектующая'} instanceof SimpleXMLElement) {
                foreach ($item->{'Комплектующие'}->{'Комплектующая'} as $offer) {
                    $this->product_offer_list[$element['id_1c']][] = trim((string)$offer);
                }
            }
            $this->product_list[$element['id_1c']] = $element;
        }

        unset($import);


        $data['element_deactivate'] = array_diff($data['all_exist_code'], $data['all_code']);

        foreach ($data['element_deactivate'] as $key => $code) {

            if ($element = Product::model()->findByAttributes([
                'code' => $code
            ])
            ) {
                if ($element->status == 1) {
                    array_push($report['element_deactivate'], (object)[
                        'id' => $element->id,
                        'code' => $code
                    ]);
                    // $element->status = 0;
                    $element->save();
                    $report['total_deactivate']++;
                }
            } else if ($element = ProductSource::model()->findByAttributes([
                'code' => $code
            ])
            ) {
                if ($element->status == 1) {
                    array_push($report['element_source_deactivate'], (object)[
                        'id' => $element->id,
                        'code' => $code
                    ]);
                    // $element->status = 0;
                    $element->save();
                    $report['total_deactivate']++;
                }
            }
        }

        /**
         * Перебор всех элементов из файла, поиск обновляемых, добавляемых товаров,
         * параметров товаров, определение рекомендуемых товаров
         */
        foreach ($this->product_list as $id => $element) {

            //Проверка товаров на существование в б.д.
            if (isset($data['check'][$element['code']])) {
                $report['total_exists_elements']++;
                unset($data['check'][$element['code']]);//удаляем элемент из массива
                $data['update_elements'][$element['id_1c']] = $element;

            } else {
                // если не существует в б.д. то будет добавлен в временную таблицу

                $data['create_elements'][$element['id_1c']] = $element;

            }

            if (isset($data['check_parameters'][$element['code']])) {
                unset($data['check_parameters'][$element['code']]);//удаляем элемент из массива
                $data['update_parameters'][$element['id_1c']] = $element;
            }
            //определяем параметры товаров
            if (isset($this->product_offer_list[$element['id_1c']])) {
                $data['create_parameters'][$element['id_1c']]['code'] = $element['sku'];
                foreach ($this->product_offer_list[$element['id_1c']] as $_id => $_element) {
                    if (isset($this->product_list[$_element])) {
                        $data['create_parameters'][$element['id_1c']]['parameters'][] = $this->product_list[$_element];
                        if ($element['code'] == 'С-000089323') {
                            var_dump(4);
                        }
                    }
                }
            }
        }

        $log = '';

        /**
         * Обновление существующих позиций
         */
        if (count($data['update_elements'])) {
            foreach ($data['update_elements'] as $element) {
                $element = (object)$element;
                $products = ProductImport::model()->findAll('code = :code',
                    array(':code' => trim($element->code)));
                foreach ($products as $item) {
                    $report['total_find_element']++;
                    $update = false;
                    if ($element->sum_sales > 0 && $item->sum_sales <> $element->sum_sales) {
                        $log .= "Изменилась сумма продаж '{$item->title} (id: {$item->id}, код:{$item->code})' на {$element->popular} <br />";
                        $item->sum_sales = $element->sum_sales;
                        $update = true;
                    }

                    if ($element->popular > 0 && $item->popular <> $element->popular) {
                        $log .= "Изменилось кол-во продаж'{$item->title} (id: {$item->id}, код:{$item->code})' на {$element->popular} <br />";
                        $item->popular = $element->popular;
                        $update = true;
                    }
                    if ($element->availability > 0 && $item->availability <> $element->availability) {
                        $log .= "Изменилось  доступность '{$item->title} (id: {$item->id}, код:{$item->code})' на {$element->availability} <br />";
                        $item->availability = $element->availability;
                        $update = true;
                    }
//                    if ($item->status == 0) {
//                        $update = true;
//                        $item->status = 1;
//                        $report['total_activated']++;
//                        array_push($report['element_activated'], (object)[
//                            'id' => $item->id,
//                            'code' => $item->code
//                        ]);
//                    }
                    if ($item->multiplicity <> $element->multiplicity) {
                        $log .= "Изменилась кратность заказа '{$item->title} (id: {$item->id}, код:{$item->code})' на {$element->multiplicity} <br />";
                        $item->multiplicity = $element->multiplicity;
                        $update = true;
                    }
                    if ($item->ordered != $element->ordered) {
                        $log .= "Изменилась статус '{$item->title} (id: {$item->id}, код:{$item->code})' на {$element->ordered} <br />";
                        $item->ordered = $element->ordered;
                        $update = true;
                    }
                    if ($item->pallet <> $element->pallet) {
                        $log .= "Изменилась кол-во паллет '{$product->title} (id: {$item->id}, код:{$item->code})' с {$item->pallet} на. {$element->pallet} <br />";
                        $item->pallet = $element->pallet;
                        $update = true;
                    }

                    if ((string)$item->price != (string)$element->price) {
                        $log .= "Изменилась цена на продукт '{$item->title} (id: {$item->id}, код:{$item->code})' с {$item->price} на. {$element->price} <br />";
                        $item->price = $element->price;
                        $update = true;
                    }

                    if ($update) {
                        $item->updated_at = date('Y-m-d H:i:s');
                        $item->save(false);
                        $report['total_updated']++;
                    }
                }
            }
        }
        /**
         * Обновление доп. товаров
         */
        if (count($data['update_parameters'])) {
            $paramLog = '';
            foreach ($data['update_parameters'] as $element) {
                $element = (object)$element;

                if (isset($element->code)) {
                    $products = ProductParamImport::model()->findAll('sku = :sku OR code = :code', [
                        ':sku' => $element->sku, ':code' => $element->code
                    ]);


                    foreach ($products as $product) {
                        if ($product !== null) {
                            $report['total_find_parameter']++;
                            $foundInThisStep = true;
                            $product->title_1c = $element->title_1c;
                            $update = false;
                            if ($product->pallet <> $element->pallet) {
                                $paramLog .= "Изменилась кол-во паллет '{$product->title} (id: {$product->id}, код:{$element->code})' с {$product->pallet} на. {$element->code} <br />";
                                $product->pallet = $element->pallet;
                                $update = true;
                            }
                            if ($product->ordered != $element->ordered) {
                                $paramLog .= "Изменилась статус '{$product->title} (id: {$product->id}, код:{$element->code})' на {$element->ordered} <br />";
                                $product->ordered = $element->ordered;
                                $update = true;
                            }

                            if ((string)$product->price != (string)$element->price) {
                                $paramLog .= "Изменилась цена на продукт '{$product->title} (id: {$product->id}, код:{$element->code})' с {$product->price} на. {$element->price} <br />";
                                $product->price = $element->price;
                                $update = true;
                            }
                            if ($update) {
                                $product->save(false);
                                $report['total_updated_parameters']++;
                            }
                        }
                    }
                }
            }
        }

        /**
         * Добавление\обновление импортируемых товаров
         */
        if (count($data['create_elements'])  ) {
            foreach ($data['create_elements'] as $element) {
                $dop_product = ProductParamImport::model()->findByAttributes(['code' => $element['code']]);
                if ($element['title'] && (!$dop_product)) {
                    $manufacturer = null;
                    if ($element['manufacturer']) {
                        $criteria = new CDbCriteria();
                        $criteria->compare('title_ru', $element['manufacturer'], 'OR');
                        $criteria->compare('title_en', $element['manufacturer'], 'OR');
                        $manufacturer = Manufacturer::model()->find($criteria);
                    }
                    /**
                     * 9.Поле Аналог- Перечень идентификаторов по коду 1С – это аналоги товара. Для работы с этими аналогами – ДЕЛАЕМ еще одну вкладку в карточке товара, как по сопутствующим товарам. Товары идут идентификаторами через зяпятую и соответственно мы выводим их после активации на сайте В СЛАЙДЕРЕ ГОРИЗОНТАЛЬНОМ АНАЛОГИЧНЫЕ ТОВАРЫ.
                     * 10. Комплектующие  ЭТО НАША ВКЛАДКА ПАРАМЕТРЫ. Таким образом, мы сразу к основному товару заводим доп.товары.
                     * 11.СоставКомплекта сделать подгруппу.
                     * ЭТО Состав комплекта (товары в сборе). Сейчас у нас и доп.товары и состав наборного товара. Согласовать формат.
                     * 15. Дата создания
                     */
                    $new_element = ProductSource::model()->findByAttributes([
                        'sku' => $element['sku']
                    ]);
                    $created = false;
                    if (!$new_element) {
                        $new_element = new ProductSource();
                        $created = true;
                    } else {
                        if ($new_element->status == 0) {
                            $new_element->status = 1;
                            $report['total_activated_sources']++;//всего активировано
                        }
                    }

                    $new_element->attributes = [
                        'title' => $element['title'],
                        'title_1c' => $element['title_1c'],
                        'code' => $element['code'],
                        'sku' => $element['sku'],
                        'man_id' => ($manufacturer) ? $manufacturer->id : 0,
                        'country' => $element['country'],
                        'details' => $element['description'],
                        'multiplicity' => $element['multiplicity'],
                        'amount' => $element['amount'],
                        'weight' => $element['weight'],
                        'price' => $element['price'],
                        'date_created' => $element['date_created'],
                        //
                        'alias' => TextHelper::translit($element['title']),
                        'cat_id' => 0,
                        'ntype"' => 0,
                        'description' => $element['manufacturer'] . "r\n" . $element['parent'],
                        'options' => ''
                    ];
                    /**
                     * Сохранение новой позиции
                     */

                    if ($created) {
                        if ($new_element->validate()) {
                            $new_element->save();
                            $report['element_created'][$element['sku']] = (object)[
                                'id' => $new_element->id,
                                'code' => $element['sku']
                            ];
                            $report['total_created']++;
                        } else {
                            var_dump($new_element->getErrors());
                        }
                    } else {
                        $new_element->man_id = ($manufacturer) ? $manufacturer->id : 0;
                        $new_element->country = $element['country'];
                        $new_element->details = $element['description'];
                        $new_element->multiplicity = $element['multiplicity'];
                        $new_element->amount = $element['amount'];
                        $new_element->weight = $element['weight'];
                        $new_element->price = $element['price'];
                        $new_element->date_created = $element['date_created'];
                        $new_element->updated_at = date('Y-m-d H:i:s');
                        $new_element->save();
                        $report['total_updated_source']++;//всего обновлено
                    }
                    /**
                     * Обработка изображений
                     */
                    if (count($element['images'])) {
                        foreach ($element['images'] as $key => $file) {
                            $new_file = $file;
                            if (file_exists($root_path . '/1c/images/' . $file)) {
                                if (file_exists($root_path . '/images/Product/' . $file)) {
                                    $new_file = $key . '+' . $new_element->id . '+' . $file;
                                    if (!$image = ProductSourceImages::model()->findByAttributes([
                                        'product_id' => $new_element->id,
                                        'name' => $new_file
                                    ])
                                    ) {
                                        if (@copy($root_path . '/1c/images/' . $file,
                                            $root_path . '/images/Product/' . $new_file)
                                        ) {
                                            $image = new ProductSourceImages();
                                            $image->attributes = [
                                                'product_id' => $new_element->id,
                                                'name' => $new_file
                                            ];
                                            $image->save();
                                        }
                                    }
                                }
                            }
                        }
                    }

                }
            }
        }

        if (count($data['create_parameters']) ) {
            foreach ($data['create_parameters'] as $key => $elements) {
                $item = ProductSource::model()->findByAttributes([
                    'code' => $elements['code']
                ]);
                if (is_array($elements['parameters'])) {
                    foreach ($elements['parameters'] as $element) {
                        $new = false;
                        if (!$parameter = ProductSourceParam::model()->findByAttributes([
                            'sku' => $element['sku'],
                            'product_id' => $item->id,
                        ])
                        ) {
                            $new = true;
                            $parameter = new ProductSourceParam();
                        }
                        if ($item->id) {
                            $parameter->attributes = [
                                'sku' => $element['sku'],
                                'code' => $element['sku'],
                                'product_id' => $item->id,
                                'title' => '--',
                                'value' => $element['title'],
                                'order' => $key + 1,
                                'weight' => $element['weight'],
                                'price' => $element['price'],
                                'pallet' => $element['pallet'],
                                'multiplicity' => $element['multiplicity'],
                                'date_created' => ($element['date_created']) ? date('Y-m-d H:i:s', $element['date_created']) : null,
                            ];
                            if ($parameter->validate()) {
                                $parameter->save();
                                if ($new) {
                                    $report['total_parameter_created']++;
                                } else {
                                    $report['total_parameter_update']++;
                                }
                            } else {
                                var_dump($parameter->getErrors());

                            }
                        }
                    }
                }
            }
        }

        $debug = 'Отработало за ' . gmdate('H:i:s', sprintf('%0.5f', Yii::getLogger()->getExecutionTime())) .
            ' Скушано памяти: ' . round(memory_get_peak_usage() / (1024 * 1024), 2) . 'MB <br />';
        $debug .= 'Всего товаров в импорте: ' . $report['total_elements'] . '<br>';
        $debug .= 'Всего товаров на сайте (активных): ' . ProductImport::model()->countByAttributes(array(
                'status' => 1
            )) . '<br>';
        $debug .= 'Всего доп. товаров на сайте: ' . ProductParamImport::model()->count() . '<br>';
        $debug .= '----------------------------------------<br/>';
        if (isset($data['create_elements'])) {
            $debug .= 'Подготовлено к добавлению товаров: ' . count($data['create_elements']) . '<br>';
        }
        if (isset($data['create_parameters'])) {
            $debug .= 'Подготовлено к добавлению доп. товаров: ' . count($data['create_parameters']) . '<br>';
        }
        if (isset($data['update_elements'])) {
            $debug .= 'Подготовлено к обновлению товаров: ' . count($data['update_elements']) . '<br>';
        }
        if (isset($data['update_parameters'])) {
            $debug .= 'Подготовлено к обновлению доп. товаров: ' . count($data['update_parameters']) . '<br>';
        }

        /**
         * Отправить сообщение о устаревшем файле импорта
         */
        $send_notify = true;

        $debug .= '----------------------------------------<br/>';
        if ($report['total_find_element']) {
            $debug .= 'Найдено товаров в б.д. для обновления: ' . $report['total_find_element'] . '<br>';
        }
        if ($report['total_updated']) {
            $send_notify = false;
            $debug .= 'Обновлено товаров: ' . $report['total_updated'] . '<br>';
        }
        if ($report['total_find_parameter']) {
            $debug .= 'Найдено доп. товаров в б.д. для обновления: ' . $report['total_find_parameter'] . '<br>';
        }
        if ($report['total_updated_parameters']) {
            $send_notify = false;
            $debug .= 'Обновлено доп. товаров: ' . $report['total_updated_parameters'] . '<br>';
        }
        if ($report['total_activated']) {
            $send_notify = false;
            $debug .= 'Активировано товаров: ' . $report['total_activated'] . '<br>';
        }
        $debug .= '----------------------------------------<br/>';
        if (isset($report['total_created'])) {
            $send_notify = false;
            $debug .= 'Создано товаров (источник): ' . $report['total_created'] . '<br>';
        }
        if (isset($report['total_updated_source'])) {
            $debug .= 'Обновлено товаров (источник): ' . $report['total_updated_source'] . '<br>';
        }
        if (isset($report['total_parameter_created'])) {
            $debug .= 'Создано доп. товаров (источник): ' . $report['total_parameter_created'] . '<br>';
        }
        if (isset($report['total_parameter_update'])) {
            $debug .= 'Обновлено доп. товаров (источник): ' . $report['total_parameter_update'] . '<br>';
        }
        $debug .= '----------------------------------------<br/>';
        if (isset($report['element_deactivate']) && count($report['element_deactivate'])) {
            $send_notify = false;
            $debug .= 'Отключено товаров: <br /><pre>';
            $codes = [];
            foreach ($report['element_deactivate'] as $idx => $item) {
                $item = (object)$item;
                $codes[] = '<a href="/admin/product/update?id=' . $item->id . '" target="_blank">' .
                    $item->code . '</a>';
            }
            $debug .= implode(',', $codes);
            $debug .= '</pre><br />';
        }

        if (isset($report['element_activated']) && count($report['element_activated'])) {
            $send_notify = false;
            $debug .= 'Включено товаров: <br /><pre>';
            $codes = [];
            foreach ($report['element_activated'] as $idx => $item) {
                $item = (object)$item;
                $codes[] = '<a href="/admin/product/update?id=' . $item->id . '" target="_blank">' .
                    $item->code . '</a>';
            }
            $debug .= implode(',', $codes);
            $debug .= '</pre><br />';
        }
        if (isset($report['element_source_deactivate']) && count($report['element_source_deactivate'])) {
            $send_notify = false;
            $debug .= 'Отключено товаров (источник): <br /><pre>';
            $codes = [];
            foreach ($report['element_source_deactivate'] as $idx => $item) {
                $item = (object)$item;
                $codes[] = '<a href="/admin/product/update_import?id=' . $item->id . '" target="_blank">' .
                    $item->code . '</a>';
            }
            $debug .= implode(',', $codes);
            $debug .= '</pre><br />';
        }
        if (isset($report['element_created']) && count($report['element_created'])) {
            $send_notify = false;
            $debug .= 'Добавлено товаров (источник): <br /><pre>';
            $codes = [];
            foreach ($report['element_created'] as $idx => $item) {
                $item = (object)$item;
                $codes[] = '<a href="/admin/product/update_import?id=' . $item->id . '" target="_blank">' .
                    $item->code . '</a>';
            }
            $debug .= implode(',', $codes);
            $debug .= '</pre><br />';
        }

        $debug .= ' <br>';
        $logModel = new Log();
        $logModel->title = "Синхронизация 1С";
        $logModel->text = $debug . ' <strong>Параметры:</strong ><br> ' .
            $paramLog . '<br >'
            . '<strong> Товары:</strong ><br> ' . $log;
        $logModel->save();


        if ($send_notify) {
            $message = new YiiMailMessage;
            $message->view = 'emptyImport';
            $message->setBody(array('order' => $this), 'text/html');
            $message->setSubject('Ошибка импорта');
            $message->addTo('zinasokolowa@yandex.ru');
            $message->from = array(Yii::app()->params['robotEmail'] => 'НемКа');
            Yii::app()->mail->send($message);
        }


        Yii::app()->end();
    }

    /**
     * Unzip the source_file in the destination dir
     *
     * @param   string      The path to the ZIP-file.
     * @param   string      The path where the zipfile should be unpacked, if false the directory of the zip-file is used
     * @param   boolean     Indicates if the files will be unpacked in a directory with the name of the zip-file (true) or not (false) (only if the destination directory is set to false!)
     * @param   boolean     Overwrite existing files (true) or not (false)
     *
     * @return  boolean     Succesful or not
     */
    public function unzip($src_file, $dest_dir = false, $create_zip_name_dir = true, $overwrite = true)
    {
        if ($zip = zip_open($src_file)) {
            if ($zip) {
                $splitter = ($create_zip_name_dir === true) ? "." : "/";
                if ($dest_dir === false) {
                    $dest_dir = substr($src_file, 0, strrpos($src_file, $splitter)) . "/";
                }

                // Create the directories to the destination dir if they don't already exist
                $this->create_dirs($dest_dir);

                // For every file in the zip-packet
                while ($zip_entry = zip_read($zip)) {
                    // Now we're going to create the directories in the destination directories

                    // If the file is not in the root dir
                    $pos_last_slash = strrpos(zip_entry_name($zip_entry), "/");
                    if ($pos_last_slash !== false) {
                        // Create the directory where the zip-entry should be saved (with a "/" at the end)
                        $this->create_dirs($dest_dir . substr(zip_entry_name($zip_entry), 0, $pos_last_slash + 1));
                    }

                    // Open the entry
                    if (zip_entry_open($zip, $zip_entry, "r")) {

                        // The name of the file to save on the disk
                        $file_name = $dest_dir . zip_entry_name($zip_entry);

                        // Check if the files should be overwritten or not
                        if ($overwrite === true || $overwrite === false && !is_file($file_name)) {
                            // Get the content of the zip entry
                            $fstream = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

                            file_put_contents($file_name, $fstream);
                            // Set the rights
                            chmod($file_name, 0777);
                            //echo "save: " . $file_name . "<br />";
                        }
                        // Close the entry
                        zip_entry_close($zip_entry);
                    }
                }
                // Close the zip-file
                zip_close($zip);
            }
        } else {
            return false;
        }

        return true;
    }

    /**
     * This function creates recursive directories if it doesn't already exist
     *
     * @param String  The path that should be created
     *
     * @return  void
     */
    public function create_dirs($path)
    {
        if (!is_dir($path)) {
            $directory_path = "";
            $directories = explode("/", $path);
            array_pop($directories);

            foreach ($directories as $directory) {
                $directory_path .= $directory . "/";
                if (!is_dir($directory_path)) {
                    mkdir($directory_path);
                    chmod($directory_path, 0777);
                }
            }
        }
    }

    /**
     * @param $item
     * @return float|mixed|string
     */
    protected function _getPrice($item)
    {
        $itemPrice = null;
        $itemPrice = $item->xpath('ЗначениеСвойства[@ИдентификаторСвойства="Цена"]');
        if ($itemPrice) {
            $itemPrice = current($itemPrice);
            $itemPriceAttributes = $itemPrice->attributes();
            $price = trim((string)$itemPriceAttributes['Значение']);
            $price = str_replace(array(',', " "), array('.', ''), $price);
            $price = preg_replace("/[^0-9.]/", '', $price);
            return $price;
        }
        return 0.00;
    }

    /**
     * @param $item
     * @return string
     */
    protected function _getCurrency($item)
    {
        $itemPriceCurrency = $item->xpath('ЗначениеСвойства[@ИдентификаторСвойства="Валюта"]');
        if ($itemPriceCurrency) {
            $itemPriceCurrency = current($itemPriceCurrency);
            $itemPriceAttributes = $itemPriceCurrency->attributes();
            $currency = trim((string)$itemPriceAttributes['Значение']);
            return $currency;
        }
        return '';
    }


    /**
     * @param $node
     * @param $name
     * @param string $default
     * @return string
     */
    protected function getItemPropertiesAttributesValue($node, $name, $default = '')
    {
        $item = $node->xpath('ЗначениеСвойства[@ИдентификаторСвойства="' . $name . '"]');
        if ($item) {
            $item = current($item);
            $itemPriceAttributes = $item->attributes();
            return trim((string)$itemPriceAttributes['Значение']);
        }
        return $default;
    }

    /**
     * @param $node
     * @param $name
     * @return mixed
     */
    protected function getItemPropertiesAttributes($node, $name)
    {
        $item = $node->xpath('ЗначениеСвойства[@ИдентификаторСвойства="' . $name . '"]');
        if ($item) {
            $item = current($item);
            $itemPriceAttributes = $item->attributes();
            return $itemPriceAttributes;
        }
        return null;
    }

    /**
     * Поиск кол-ва паллет в заголовке
     *
     * @param $element
     * @return string
     */
    protected function _getPallete($element)
    {
        $match = null;
        preg_match('/[\(](\d{2,3})[\)]/', $element['title_1c'], $match);
        if (!isset($match[1])) {
            preg_match('/[\(](\d{2,3}) ?шт.?\/под.?[\)]/', $element['title_1c'], $match);
        }
        if (!isset($match[1])) {
            preg_match('/\/(\d{2,3}) шт/', $element['title_1c'], $match);
        }
        if (!isset($match[1])) {
            preg_match('/[\(](\d{2,3})\/под[\)]/', $element['title_1c'], $match);
        }
        if (!isset($match[1])) {
            preg_match('/[\(]\d{2,3}кг\/(\d{2,3})шт[\)]/', $element['title_1c'], $match);
        }
        if (!isset($match[1])) {
            preg_match('/[\(].?\d{2,3}кг\/(\d{2,3})[\)]/', $element['title_1c'], $match);
        }
        if (isset($match[1])) {
            return $match[1];
        }
        return '';
    }

    /**
     * в названиях встречаются &
     *
     * @param $file
     * @return mixed|string
     */
    private function cleanXml($file)
    {
        $xml = file_get_contents($file);
        $xml = preg_replace('/[&]/i', '&amp;', $xml);
        $xml = preg_replace('/<>/', '&lt;&gt;', $xml);
        $xml = preg_replace("/\s+/", " ", $xml);
        return $xml;
    }

    /**
     * @return string
     */
    private function checkFile()
    {
        $root = dirname(dirname(dirname(__FILE__) . '../') . '../');

        $this->unzip($root . '/1c/offers/import2.zip', $root . '/1c/offers/', true);
        $file = $root . '/1c/offers/import2.xml';
        ini_set('max_execution_time', 0);
        if (!file_exists($file)) {
            echo 'Xml files not found :C ' . PHP_EOL;
            Yii::app()->end();
        }
        return $file;
    }
}
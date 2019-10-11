<?php
/**
 * Created by PhpStorm.
 * User: dg
 * Date: 30.03.16
 * Time: 17:26
 */

namespace App\Core\Form;


interface FormInterface
{
    /**
     * Название формы для интерфейса администрирования
     *
     * @return string
     */
    public function getName();

    /**
     * Псевдоним формы описывающий назначение
     *
     * @return string
     */
    public function getAction();

    /**
     * Обработка входящего текста, замена маркера на шаблон формы
     *
     * @param array $params
     * @return string
     */
    public function load(array $params);

    /**
     * Обработка POST запроса
     *
     * @param \Closure $callback
     * @return mixed
     */
    public function action(\Closure $callback);

    /**
     * Возвращает псевдоним маршрута
     *
     * @return mixed
     */
    public function getRoute();
}
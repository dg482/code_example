<?php
/**
 * Created by PhpStorm.
 * User: dg
 * Date: 30.03.16
 * Time: 17:31
 */

namespace App\Core\Form;



abstract class Forms
{
    public $project = null;

    /**
     * Получить список всех форм проекта
     *
     * @return mixed
     */
    public abstract function getList();

    /**
     * Получить форму по имени
     *
     * @param $name
     * @return mixed
     */
    public abstract function getForm($name);
}
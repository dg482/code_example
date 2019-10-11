<?php
/**
 * Created by PhpStorm.
 * User: dg
 * Date: 30.03.16
 * Time: 17:06
 */

namespace App\Core\Form;


class Form implements FormInterface
{

    protected $name = '';

    protected $action = '';

    protected $route = '';

    /**
     * @param array $params
     * @return string
     */
    public function load(array $params)
    {

    }

    public function action(\Closure $callback)
    {
        if ($callback instanceof \Closure) {
            $callback($this);
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function getAction()
    {
        return $this->action;
    }

    /**
     * Возвращает псевдоним маршрута
     *
     * @return mixed
     */
    public function getRoute()
    {
        // TODO: Implement getRoute() method.
        return $this->route;
    }
}
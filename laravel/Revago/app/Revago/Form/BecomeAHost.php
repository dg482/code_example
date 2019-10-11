<?php
/**
 * Created by PhpStorm.
 * User: dg
 * Date: 30.03.16
 * Time: 17:10
 */

namespace App\Revago\Form;


use App\Core\Form\Form as BaseForm,
    App\Core\Form\FormInterface;

class BecomeAHost extends BaseForm implements FormInterface
{

    protected $name = 'become_a_host';

    protected $action  = 'become_a_host';

    protected $route = 'form/become-a-host';

    public function load(array $params)
    {
        $params['data']['form'] = $this;
        return str_replace('{' . $this->name . '}',
            view('forms.' . $this->name, $params['data'])->render(), $params['text']);
    }


    public function getName()
    {
        return 'Заявка на сдачу жилья в Испании';
    }


    /**
     * Возвращает псевдоним маршрута
     *
     * @return mixed
     */
    public function getRoute()
    {
        return $this->route;
    }
}
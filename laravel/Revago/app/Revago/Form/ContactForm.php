<?php
/**
 * Created by PhpStorm.
 * User: dg
 * Date: 31.03.16
 * Time: 11:38
 */

namespace App\Revago\Form;

use App\Core\Form\Form as BaseForm,
    App\Core\Form\FormInterface;

class ContactForm extends BaseForm implements FormInterface
{
    protected $name = 'contact';

    protected $action = 'contact';

    protected $route = 'form/contact';

    public function load(array $params)
    {
        $params['data']['form'] = $this;
        return str_replace('{' . $this->name . '}',
            view('forms.' . $this->name, $params['data'])->render(), $params['text']);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Сообщение с сайта Revago';
    }

    public function getAction()
    {
        return $this->action;
    }
}
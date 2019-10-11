<?php
/**
 * Created by PhpStorm.
 * User: dg
 * Date: 30.03.16
 * Time: 17:55
 */

namespace App\Revago\Form;

use App\Core\Form\Form;
use App\Core\Form\Forms as BaseForms;

class Forms extends BaseForms
{


    /**
     * Получить форму по имени
     *
     * @param $name
     * @return Form
     */
    public function getForm($name)
    {
        // TODO: Implement getForm() method.
        $result = [];
        $handlers_dir = dirname(__FILE__) . DIRECTORY_SEPARATOR;
        $files = \File::allFiles($handlers_dir);
        foreach ($files as $file) {
            $_name = "App\Revago\Form\\" . substr($file->getFileName(), 0, -4);

            if ($name <> __CLASS__) {
                $prop = new $_name();
                if ($prop->getAction() == $name) {
                    return $prop;
                }
            }
        }
        return null;
    }

    /**
     * Получить список всех форм проекта
     *
     * @return mixed
     */
    public function getList()
    {
        // TODO: Implement getList() method.
        $result = [];
        $handlers_dir = dirname(__FILE__) . DIRECTORY_SEPARATOR;
        $files = \File::allFiles($handlers_dir);
        foreach ($files as $file) {
            $name = "App\Revago\Form\\" . substr($file->getFileName(), 0, -4);

            if ($name <> __CLASS__) {
                $prop = new $name();
                $result[$prop->getAction()] = $prop->getName();
            }
        }

        return $result;
    }

    public function getAction()
    {
        return null;
    }
}
<?php namespace App\Revago;

//core
use App\Core\CatalogItemPrices as CoreCatalogItemPrices,
    App\Core\CurrencyConverter,
    App\User;

class CatalogItemPrices extends CoreCatalogItemPrices
{


    /**
     * Форматирует цену с учетом валюты пользователя и добавляет знак валюты
     *
     * @return mixed|string
     */
    public function getPriceFormat($period = null)
    {
        switch (User::getCurrency()) {
            case 'rub':
                return CurrencyConverter::_($this->price, CurrencyConverter::RUB);
            case 'eur':
                return ceil($this->price) . ' €';
        }
    }

    /**
     * Форматирует цену с учетом валюты пользователя
     *
     * @param $days
     * @return float|string
     */
    public function getDaysToConvert($days)
    {
        switch (User::getCurrency()) {
            case 'rub':
                return CurrencyConverter::_(($this->price * $days), CurrencyConverter::RUB, null, false);
            case 'eur':
                return ceil($this->price * $days);
        }
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: kamol
 * Date: 7/30/2022
 * Time: 10:57 PM
 */

namespace Service\Currency;


class ExchangeRate
{
    protected static $exchangeRate;

    public function exchange($amount, $currency)
    {

        if (isset(self::$exchangeRate)) {
            return $amount / self::$exchangeRate['rates'][$currency];
        }
        try {
            self::$exchangeRate = json_decode(file_get_contents('https://developers.paysera.com/tasks/api/currency-exchange-rates'), true);
            return $amount / self::$exchangeRate['rates'][$currency];
        } catch (\Exception $exception) {
            echo $exception->getMessage();
            exit(1);
        }

    }

    public function exchangeTo($amount, $currency){
        if(isset(self::$exchangeRate)){
            return $amount * self::$exchangeRate['rates'][$currency];
        }
        try {
            self::$exchangeRate = json_decode(file_get_contents('https://developers.paysera.com/tasks/api/currency-exchange-rates'), true);
            return $amount * self::$exchangeRate['rates'][$currency];

        } catch (\Exception $exception) {
            echo $exception->getMessage();
            exit(1);
        }
    }



}

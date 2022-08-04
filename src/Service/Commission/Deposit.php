<?php

namespace Service\Commission;

use Service\Interfaces\CommissionInterface;

class Deposit implements CommissionInterface
{
    Const DEPOSIT_FEE = '0.03';

    private $amount;

    public function __construct($amount)
    {
        $this->amount = $amount;
    }


    public function output()
    {

        $commission = ($this->amount * self::DEPOSIT_FEE) / 100;

        return number_format(round($commission, 2), 2);

    }
}
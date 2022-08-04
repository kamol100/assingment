<?php
/**
 * Created by PhpStorm.
 * User: kamol
 * Date: 7/31/2022
 * Time: 7:48 AM
 */

namespace Service\Commission;


use Service\Currency\ExchangeRate;
use Service\Interfaces\CommissionInterface;

class Withdraw implements CommissionInterface
{
    const PRIVATE_USER_WITHDRAW_FEE = '0.3';

    const BUSINESS_USER_WITHDRAW_FEE = '0.5';

    protected $user;

    protected $date;

    protected $operation;

    protected $amount;

    protected $currency;

    protected $transactionKey;

    protected $weeklyFreeLimit = 1000;

    protected $weeklyFreeTransactionLimit = 3;

    protected $weeklyTotalWithdraw = [];

    private $weeklyTransactionCount = 0;

    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    public function setTransactionKey($transactionKey)
    {
        $this->transactionKey = $transactionKey;

        return $this;
    }

    public function getTransactionKey(){
        return $this->transactionKey;
    }


    public function getUserType()
    {
        return $this->user->getType();
    }

    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    public function setOperation($operation)
    {
        $this->operation = $operation;

        return $this;
    }

    public function setAmount($amount){
        $this->amount = $amount;

        return $this;
    }

    public function getAmount(){
        return $this->amount;
    }

    public function setCurrency($currency){
        $this->currency = $currency;

        return $this;
    }

    public function getCurrency(){
        return $this->currency;
    }

    public function getOperation()
    {
        return $this->operation;
    }


    public function toEuro($amount, $currency)
    {
        return (new ExchangeRate())->exchange($amount, $currency);

    }


    public function calculateWeeklyTotalWithdraw()
    {
        $amount_in_euro = $this->toEuro($this->getAmount(), $this->getCurrency());

        if (isset($this->weeklyTotalWithdraw[$this->getTransactionKey()]) &&
            count($this->weeklyTotalWithdraw[$this->getTransactionKey()]) <= $this->weeklyFreeTransactionLimit) {

            $currentTotal = array_sum($this->weeklyTotalWithdraw[$this->getTransactionKey()]);

            $this->weeklyTransactionCount++;

            if($currentTotal > $this->weeklyFreeLimit){
                return $currentTotal;
            }
            $this->weeklyTotalWithdraw[$this->getTransactionKey()][] = $amount_in_euro;

            return array_sum($this->weeklyTotalWithdraw[$this->getTransactionKey()]);
        }

        $this->weeklyTransactionCount = 1;
        $this->weeklyTotalWithdraw[$this->getTransactionKey()][] = $amount_in_euro;


        return $amount_in_euro;
    }

    public function commissionAbleAmount()
    {
        $total = $this->calculateWeeklyTotalWithdraw();

        $withdrawItems = count($this->weeklyTotalWithdraw[$this->getTransactionKey()]);

        if ($total > $this->weeklyFreeLimit && $this->weeklyTransactionCount <= $withdrawItems) {
            $result = $total - $this->weeklyFreeLimit;
            if($this->getCurrency() != 'EUR'){
                 return (new ExchangeRate())->exchangeTo($result, $this->getCurrency());
            }
           return $result;

        }
        if($total <= $this->weeklyFreeLimit && $this->weeklyTransactionCount <= $this->weeklyFreeLimit){
            return 0;
        }
        if($this->weeklyTransactionCount > $withdrawItems){
            return $this->getAmount();
        }

        return $this->getAmount();
    }

    public function output()
    {
        if ($this->getOperation() == 'withdraw' && $this->getUserType() == 'private') {

            $commission = $this->commissionAbleAmount();
            $commission = ($commission * self::PRIVATE_USER_WITHDRAW_FEE) / 100;

            return number_format(round($commission, 2), 2);
        }

        if ($this->getOperation() == 'withdraw' && $this->getUserType() == 'business') {
            $commission = ($this->getAmount() * self::BUSINESS_USER_WITHDRAW_FEE) / 100;

            return number_format(round($commission, 2), 2);
        }
    }

}

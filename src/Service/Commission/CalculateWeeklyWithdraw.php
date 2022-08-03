<?php
/**
 * Created by PhpStorm.
 * User: kamol
 * Date: 7/31/2022
 * Time: 7:48 AM
 */

namespace Service\Commission;


use Service\Currency\ExchangeRate;

class CalculateWeeklyWithdraw
{
    protected $user;

    protected $date;

    protected $operation;

    protected $rowId;

    protected $weeklyPrivateUserWithdraw = [];

    protected $weeklyFreeLimit = 1000;

    protected $weeklyFreeTransactionLimit = 3;

    protected $weeklyTotalWithdraw = [];

    private $weeklyTransactionCount = 0;

    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    public function setRowId($rowId)
    {
        $this->rowId = $rowId;

        return $this;
    }

    public function getUserId()
    {
        return $this->user->getId();
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

    public function getDate()
    {
        return $this->date;
    }

    public function getYear()
    {
        return date('Y', strtotime($this->getDate()));
    }

    public function getWeekNumber()
    {
        return date('W', strtotime($this->getDate()));
    }

    public function setOperation($operation)
    {
        $this->operation = $operation;

        return $this;
    }

    public function getOperation()
    {
        return $this->operation;
    }


    public function toEuro($amount, $currency)
    {
        return (new ExchangeRate())->exchange($amount, $currency);

    }

    public function getWeeklyPrivateUserWithdraw()
    {
        return $this->weeklyPrivateUserWithdraw;
    }

    public function calculateWeeklyTotalWithdraw($amount_in_euro)
    {

        if (isset($this->weeklyTotalWithdraw[$this->getUserId()][$this->getYear()][$this->getWeekNumber()]) &&
            count($this->weeklyTotalWithdraw[$this->getUserId()][$this->getYear()][$this->getWeekNumber()]) <= $this->weeklyFreeTransactionLimit) {

            $currentTotal = array_sum($this->weeklyTotalWithdraw[$this->getUserId()][$this->getYear()][$this->getWeekNumber()]);

            $this->weeklyTransactionCount++;

            if($currentTotal > $this->weeklyFreeLimit){
                return $currentTotal;
            }
            $this->weeklyTotalWithdraw[$this->getUserId()][$this->getYear()][$this->getWeekNumber()][$this->rowId] = $amount_in_euro;

            return array_sum($this->weeklyTotalWithdraw[$this->getUserId()][$this->getYear()][$this->getWeekNumber()]);
        }

        $this->weeklyTransactionCount = 1;
        $this->weeklyTotalWithdraw[$this->getUserId()][$this->getYear()][$this->getWeekNumber()][$this->rowId] = $amount_in_euro;


        return $amount_in_euro;
    }


    public function commissionAbleAmount($total, $amount, $currency)
    {
        $withdrawItems = count($this->weeklyTotalWithdraw[$this->getUserId()][$this->getYear()][$this->getWeekNumber()]);

        if ($total > $this->weeklyFreeLimit && $this->weeklyTransactionCount <= $withdrawItems) {
            $result = $total - $this->weeklyFreeLimit;
            if($currency != 'EUR'){
                 return (new ExchangeRate())->exchangeTo($result, $currency);
            }
           return $result;

        }
        if($total <= $this->weeklyFreeLimit && $this->weeklyTransactionCount <= $this->weeklyFreeLimit){
            return 0;
        }
        if($this->weeklyTransactionCount > $withdrawItems){
            return $amount;
        }

        return $amount;
    }
    public function calculateWeeklyWithdraw($amount, $currency)
    {
        if ($this->getOperation() == 'withdraw' && $this->getUserType() == 'private') {
            $amount_in_euro = $this->toEuro($amount, $currency);

            $total = $this->calculateWeeklyTotalWithdraw($amount_in_euro);
            $commission_amount = $this->commissionAbleAmount($total, $amount, $currency);

            $this->weeklyPrivateUserWithdraw[$this->getUserId()][$this->getYear()][$this->getWeekNumber()][$this->rowId] = [
                'amount_in_euro' => $amount_in_euro,
                $currency => $amount,
                'commission_amount' => $commission_amount,
                'weekly_transaction_count' => $this->weeklyTransactionCount
            ];
        }
    }
}

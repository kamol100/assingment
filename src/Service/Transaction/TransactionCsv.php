<?php
/**
 * Created by PhpStorm.
 * User: kamol
 * Date: 8/4/2022
 * Time: 8:15 AM
 */

namespace Service\Transaction;


use Service\Commission\CalculateWeeklyWithdraw;
use Service\User\User;
use Service\Commission\CalculateCommission;

class TransactionCsv
{
    protected $data;
    protected $commission = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function commission()
    {
        $weeklyWithdraw = new CalculateWeeklyWithdraw();
        
        for ($index = 0; $index < count($this->data); $index++) {
            $date = $this->data[$index][0] ?? null;
            $userId = $this->data[$index][1] ?? null;
            $user_type = $this->data[$index][2] ?? null;
            $operation_type = $this->data[$index][3] ?? null;
            $amount = $this->data[$index][4] ?? null;
            $currency = $this->data[$index][5] ?? null;
            if ($date && $userId && $user_type && $operation_type && $amount && $currency) {
                $transactionKey = $userId.'-'.date('Y-W', strtotime($date));
                $user = new User($userId, $user_type);
                $weeklyWithdraw->setUser($user)->setRowId($index)->setDate($date)->setOperation($operation_type);
                $weeklyWithdraw->calculateWeeklyWithdraw($amount, $currency);
                $commission = new CalculateCommission($weeklyWithdraw, $user, $operation_type, $date, $amount, $index);
                //  dump($commission);
                $this->commission[] = $commission->calculate();
            }
        }

        return $this->commission;
    }

}
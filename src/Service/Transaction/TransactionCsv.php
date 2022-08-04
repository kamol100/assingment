<?php
/**
 * Created by PhpStorm.
 * User: kamol
 * Date: 8/4/2022
 * Time: 8:15 AM
 */

namespace Service\Transaction;


use Service\Commission\Withdraw;
use Service\Commission\Deposit;
use Service\User\User;

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
        $weeklyWithdraw = new Withdraw();

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

                if($operation_type == 'deposit'){
                    $deposit = new Deposit($amount);
                    $this->commission[] = $deposit->output();
                }else{
                    $weeklyWithdraw->setUser($user)
                        ->setTransactionKey($transactionKey)
                        ->setDate($date)
                        ->setOperation($operation_type)
                        ->setAmount($amount)
                        ->setCurrency($currency);

                    $this->commission[] = $weeklyWithdraw->output();
                }

            }
        }

        return $this->commission;
    }

}
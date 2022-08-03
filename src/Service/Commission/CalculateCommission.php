<?php
/**
 * Created by PhpStorm.
 * User: kamol
 * Date: 7/31/2022
 * Time: 5:22 PM
 */

namespace Service\Commission;

use Service\User\User;

class CalculateCommission
{
    protected $user;

    protected $year;

    protected $weekNumber;

    protected $rowId;

    protected $operation_type;

    protected $amount;

    protected $withdrawCommissionFeePrivateUser = 0.3;

    protected $withdrawCommissionFeeBusinessUser = 0.5;

    protected $depositCommissionFee = 0.03;

    protected $weeklyWithdraw;

    public function __construct(CalculateWeeklyWithdraw $weeklyWithdraw, User $user, $operation_type, $year, $weekNumber, $amount, $rowId)
    {
        $this->user = $user;
        $this->amount = $amount;
        $this->year = $year;
        $this->operation_type = $operation_type;
        $this->weekNumber = $weekNumber;
        $this->weeklyWithdraw = $weeklyWithdraw;
        $this->rowId = $rowId;
    }

    public function calculate()
    {
        if ($this->operation_type == 'deposit') {
            return $this->depositCommission();
        }
        if ($this->operation_type == 'withdraw' && $this->user->getType() == 'business') {
            return $this->businessWithdraw();
        }

        return $this->privateUserWithdraw();
    }

    public function privateUserWithdraw()
    {
        $withdraw = $this->weeklyWithdraw->getWeeklyPrivateUserWithdraw();
        $amount = $withdraw[$this->user->getId()][$this->year][$this->weekNumber][$this->rowId]['commission_amount'];
        $commission = ($amount * $this->withdrawCommissionFeePrivateUser) / 100;

        return $this->output($commission);
    }

    public function depositCommission()
    {
        $commission = ($this->amount * $this->depositCommissionFee) / 100;

        return $this->output($commission);
    }

    public function businessWithdraw()
    {
        $commission = ($this->amount * $this->withdrawCommissionFeeBusinessUser) / 100;

        return $this->output($commission);
    }

    public function output($amount)
    {
        return number_format(round($amount, 2), 2);
    }
}

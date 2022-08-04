<?php

use PHPUnit\Framework\TestCase;
use Service\Imports\Csv;
use Service\Transaction\TransactionCsv;

class TransactionCommissionTest extends TestCase
{
    public function test_transaction_commission(){
        $file = __DIR__.'/input.csv';
        $csvData = Csv::data($file);
        $transaction = new TransactionCsv($csvData);

        $commission = $transaction->commission();

        $this->assertEquals(['0.60','0.00','0.00','0.06','1.50','0.00','0.69','0.30','0.30','3.00','0.00','0.00','8,607.39'], $commission);
    }

}
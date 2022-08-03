<?php
require_once 'vendor/autoload.php';
use Service\Imports\Csv;
use Service\Commission\CalculateWeeklyWithdraw;
use Service\Commission\CalculateCommission;
use Service\User\User;


if(isset($_POST['submit'])){
    $file = $_FILES['file']['tmp_name'];
    $csvImport = new Csv();
    $data = $csvImport::data($file);


    $weeklyWithdraw = new CalculateWeeklyWithdraw();
    for ($index = 0; $index < count($data); $index++) {
        $date = $data[$index][0] ?? null;
        $userId = $data[$index][1] ?? null;
        $user_type = $data[$index][2] ?? null;
        $operation_type = $data[$index][3] ?? null;
        $amount = $data[$index][4] ?? null;
        $currency = $data[$index][5] ?? null;
        if ($data && $userId && $user_type && $operation_type && $amount && $currency) {
            $year = date('Y', strtotime($date));
            $week_number = date('W', strtotime($date));
            $user = new User($userId, $user_type);
            $weeklyWithdraw->setUser($user)->setRowId($index)->setDate($date)->setOperation($operation_type)->calculateWeeklyWithdraw($amount, $currency);
            $commission = new CalculateCommission($weeklyWithdraw, $user, $operation_type, $year, $week_number, $amount, $index);
            //  dump($commission);
            echo $commission->calculate().'<br/>';
        }
    }

}

?>

<form method="post" action="index.php" enctype="multipart/form-data">
    <input type="file" name="file" accept=".csv">
    <input type="submit" name="submit" value="Calculate" id="">
</form>

<?php
require_once 'vendor/autoload.php';
use Service\Imports\Csv;
use Service\Transaction\TransactionCsv;


$transactionCommission = [];

if(isset($_POST['submit']))
{


    $file = $_FILES['file']['tmp_name'];

    if($file){
        $csvImport = new Csv();
        $data = $csvImport::data($file);
        $transaction = new TransactionCsv($data);
        $transactionCommission = $transaction->commission();
    }else{
        echo 'Invalid file path';
    }

}

?>
<div class="upload" style="display: flex; justify-content: space-around; margin-top: 50px">
    <form method="post" action="index.php" enctype="multipart/form-data">
        <input type="file" name="file" accept=".csv">
        <input type="submit" name="submit" value="Calculate" id="">
    </form>
</div>
<div class="transaction" style="display: flex; justify-content: space-around; margin-top: 30px">
    <?php foreach ($transactionCommission as $commission) { ?>
        <?php echo $commission; ?> <br/>
    <?php } ?>

</div>


<?php
/**
 * Created by PhpStorm.
 * User: kamol
 * Date: 7/27/2022
 * Time: 10:02 PM
 */

namespace Service\Imports;

class Csv
{
    public static function data($file)
    {
        $handle = fopen($file, 'r');

        $csvData = [];
        while (! feof($handle)) {
            $fpTotal = fgetcsv($handle, 0, ',', '\\');
            array_push($csvData, $fpTotal);
        }
        fclose($handle);

        return $csvData;
    }
}

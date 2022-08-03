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
        if (!($file = fopen($file, 'r'))) {
            throw new \RuntimeException('Failed to open file');
        }

        $csvData = [];
        while (! feof($file)) {
            $fpTotal = fgetcsv($file, 0, ',', '\\');
            array_push($csvData, $fpTotal);
        }
        fclose($file);

        return $csvData;
    }
}

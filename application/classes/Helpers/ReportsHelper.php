<?php
/**
 * Created by PhpStorm.
 * User: lusine
 * Date: 1/22/21
 * Time: 2:12 PM
 */
namespace Helpers;

class ReportsHelper {
    public static function getTotalExcept($arr, $exceptCount)
    {
        $sum = 0;
        if (count($arr)) {
            $sum = array_sum($arr) - $exceptCount;
        }
        return $sum;
    }
}
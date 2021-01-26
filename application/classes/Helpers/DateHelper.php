<?php
/**
 * Created by PhpStorm.
 * User: lusine
 * Date: 1/22/21
 * Time: 2:12 PM
 */
namespace Helpers;

class DateHelper {
    public static function getExpirationDate($date)
    {
        $dt = time() + 86400*365;
        switch ($dt) {
            case $dt <= time():
                $dt = time() + 86400*1095;
                break;
            case $dt > time():
                $dt = time() + 86400*365;
                break;
        }
        return $dt;
    }
}
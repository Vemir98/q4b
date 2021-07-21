<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 07.03.2018
 * Time: 12:37
 */
class Api_DBElements
{
    public static function getElementById($id)
    {
        return DB::query(Database::SELECT,'SELECT * FROM elements WHERE id='.$id)->execute()->as_array();
    }
    public static function getElementsCrafts($elIds)
    {
        return DB::query(Database::SELECT,'SELECT craft_id id, element_id FROM elements_cmp_crafts ecc WHERE ecc.element_id IN ('.implode(',',$elIds).')')->execute()->as_array();
    }
}
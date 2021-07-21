<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 07.03.2018
 * Time: 12:37
 */
class Api_DBCompanies
{
    public static function getCompanyCrafts($cmpId, $fields=[]){
        return DB::query(Database::SELECT,'
        SELECT ' . implode(",",$fields) . ' FROM cmp_crafts WHERE company_id='.$cmpId.' AND status="'.Enum_Status::Enabled.'" ORDER BY name ASC')->execute()->as_array();
    }
    public static function getCmpProjects($id) {
        return DB::query(Database::SELECT,'
        SELECT * FROM projects WHERE company_id='.$id)->execute()->as_array();
    }
}
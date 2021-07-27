<?php

class Api_DBModules
{
    public static function getModules()
    {
        return DB::query(Database::SELECT,'SELECT * FROM modules')->execute()->as_array();
    }
}
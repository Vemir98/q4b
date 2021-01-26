<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 13.05.2019
 * Time: 7:45
 */

class Model_QualityReport extends ORM
{
    protected $_table_name = 'quality_reports';
    protected $_created_column = ['column' => 'created_at', 'format' => true];
}
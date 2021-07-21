<?php defined('SYSPATH') OR die('No direct script access.');

class Model_CraftLabTest extends MORM
{
    protected $_table_name = 'craft_labtest';


    protected $_has_one = [
        'craftLabtestParams' => [
            'model' => 'CraftLabTestParam',
            'foreign_key' => 'cl_id',
        ],
    ];
}
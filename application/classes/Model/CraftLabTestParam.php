<?php defined('SYSPATH') OR die('No direct script access.');

class Model_CraftLabTestParam extends MORM
{
    protected $_table_name = 'craft_labtest_params';

    protected $_belongs_to = [
        'craftLabTest' => [
            'model' => 'CraftLabTest',
            'foreign_key' => 'cl_id'
        ],
    ];
}
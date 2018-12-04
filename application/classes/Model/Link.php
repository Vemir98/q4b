<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 13.01.2017
 * Time: 14:59
 */
class Model_Link extends ORM
{
    protected $_table_name = 'links';

    public function rules()
    {
        return [
            'name' => [
                ['not_empty']
            ],
            'url' => [
                ['not_empty'],
                ['url']
            ]
        ];
    }

    public function filters()
    {
        return [
            true => [
                ['htmlentities', [':value'],ENT_QUOTES],
                ['strip_tags']
            ]
        ];
    }
}
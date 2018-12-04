<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 28.08.2017
 * Time: 16:35
 */
class Model_ConstructElement extends ORM
{
    protected $_table_name = 'construction_elements';

    public function filters()
    {
        return [
            true => [
                ['htmlentities', [':value'],ENT_QUOTES],
                ['strip_tags']
            ]
        ];
    }

    public function rules(){
        return [
            'name' => [
                ['not_empty'],
                ['max_length',[':value','64']],
            ],
            'icon' => [
                ['not_empty'],
                ['max_length',[':value','64']],
            ],
            'space_count' => [
                ['not_empty'],
                ['digit'],
                ['min_length',[':value','1']],
                ['max_length',[':value','3']],

            ],
        ];
    }
}
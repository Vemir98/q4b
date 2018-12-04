<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 08.12.2016
 * Time: 3:48
 */
class Model_PrObjectType extends ORM
{
    protected $_table_name = 'pr_object_types';

    public function rules(){
        return [
            'name' => [
                ['not_empty'],
                ['max_length',[':value','50']],
            ],
            'alias' => [
                ['not_empty'],
                ['max_length',[':value','50']],
                [
                    function(Validation $valid){
                        if(preg_match('~[^a-z_]~',$this->alias)){
                            $valid->error('not_eng_lower', 'incorrect_input_data');
                        }
                    },
                    [':validation']
                ],
            ],
        ];
    }
}
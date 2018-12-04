<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 12.08.2016
 * Time: 16:37
 */
class Model_ACL_Privilege extends ORM
{
    protected $_table_name = 'privileges';

    protected $_has_many = [
        'resources' => [
            'foreign_key' => 'privilege_id',
            'far_key' => 'resource_id',
            'model' => 'ACL_Resource',
            'through' => 'resources_privileges'
        ]
    ];

    public function rules()
    {
        return array(
            'name' => array(
                array('not_empty'),
                array('max_length', array(':value', 64)),
            ),
            'alias' => array(
                array('not_empty'),
                array('max_length', array(':value', 32)),
                array(array($this, 'unique'), array('alias', ':value')),
            ),
        );
    }
}
<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 12.08.2016
 * Time: 16:36
 */
class Model_ACL_Resource extends ORM
{
    protected $_table_name = 'resources';

    protected $_has_many = [
        'privileges' => [
            'foreign_key' => 'resource_id',
            'far_key' => 'privilege_id',
            'model' => 'ACL_Privilege',
            'through' => 'resources_privileges'
        ]
    ];

    public function rules()
    {
        return array(
            'name' => array(
                array('not_empty'),
                array('max_length', array(':value', 128)),
            ),
            'alias' => array(
                array('not_empty'),
                array('max_length', array(':value', 64)),
                array(array($this, 'unique'), array('alias', ':value')),
            ),
        );
    }
}
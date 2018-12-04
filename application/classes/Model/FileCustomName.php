<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 25.10.2017
 * Time: 1:36
 */
class Model_FileCustomName extends ORM
{
    protected $_table_name = 'files_custom_names';
    protected $_primary_key = 'file_id';

    protected $_belongs_to = [
        'file' => [
            'model' => 'File',
            'foreign_key' => 'file_id'
        ]
    ];

    public function rules()
    {
        return [
            'name' => [
                ['not_empty'],
            ],
            'file_id' => [
                ['not_empty'],
                ['digit'],
            ]
        ];
    }
}
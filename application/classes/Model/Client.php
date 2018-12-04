<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 23.09.2016
 * Time: 12:27
 */
class Model_Client extends MORM
{
    protected $_table_name = 'clients';

    protected $_created_column = ['column' => 'created_at', 'format' => true];
    protected $_updated_column = ['column' => 'updated_at', 'format' => true];

    protected $_has_many = [
        'companies' => [
            'model' => 'Company',
            'foreign_key' => 'client_id'
        ],
        'users' => [
            'model' => 'User',
            'foreign_key' => 'client_id'
        ]
    ];


    /**
     * Перегрузка метода инициализации
     * Добавляем информацию о тех кто работал с текущей записью
     */
    protected function _initialize(){
        if(Auth::instance()->get_user()){
            $this->_created_by_column = ['column' => 'created_by', 'value' => Auth::instance()->get_user()->id];
            $this->_updated_by_column = ['column' => 'updated_by', 'value' => Auth::instance()->get_user()->id];
        }else{
            $this->_created_by_column = ['column' => 'created_by'];
            $this->_updated_by_column = ['column' => 'updated_by'];
        }
        parent::_initialize();
    }

    public function rules(){
        return [
            'type' => [
                ['not_empty'],
                [
                    function( Validation $valid){
                        if(!in_array($this->type,Enum_ClientType::toArray())){
                            $valid->error('type', 'invalid_client_type');
                        }
                    },
                    [':validation']
                ]
            ],
        ];
    }
}
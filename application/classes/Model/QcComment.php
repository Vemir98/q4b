<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 24.01.2018
 * Time: 15:51
 */
class Model_QcComment extends MORM
{
    protected $_table_name = "quality_control_comments";
    protected $_created_column = ['column' => 'created_at', 'format' => true];

    protected $_belongs_to = [
        'owner' => [
            'model' => 'User',
            'foreign_key' => 'created_by'
        ],
        'qc' => [
            'model' => 'QualityControl',
            'foreign_key' => 'qcontrol_id'
        ]
    ];

    protected function _initialize(){
        if(Auth::instance()->get_user()){
            $this->_created_by_column = ['column' => 'created_by', 'value' => Auth::instance()->get_user()->id];
        }else{
            $this->_created_by_column = ['column' => 'created_by'];
        }

        parent::_initialize();
    }

    public function rules(){
        return [
            'message' => [
                ['not_empty'],
                ['max_length',[':value','250']],
            ],
            'qcontrol_id' => [
                ['max_length',[':value','32']],
                ['numeric'],
            ],
        ];
    }
}
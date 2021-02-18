<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 22.04.2020
 * Time: 12:40
 */

class Model_DeliveryReport extends MORM
{
    protected $_table_name = 'delivery_reports';
    protected $_created_column = ['column' => 'created_at', 'format' => true];

    protected $_belongs_to = [
        'user' => [
            'model' => 'User',
            'foreign_key' => 'created_by'
        ],
        'company' => [
            'model' => 'Company',
            'foreign_key' => 'company_id'
        ],
        'project' => [
            'model' => 'Project',
            'foreign_key' => 'project_id'
        ],
        'object' => [
            'model' => 'PrObject',
            'foreign_key' => 'object_id'
        ],
        'floor' => [
            'model' => 'PrFloor',
            'foreign_key' => 'floor_id'
        ],
        'place' => [
            'model' => 'PrPlace',
            'foreign_key' => 'place_id'
        ],

    ];

    protected $_has_many = [
        'customers' => [
            'model' => 'Customer',
            'foreign_key' => 'del_report_id',
            'far_key' => 'customer_id',
            'through' => 'delivery_reports_customers'
        ],
        'quality_controls' => [
            'model' => 'QualityControl',
            'foreign_key' => 'del_rep_id',
        ],
        'poaFiles' => [
            'model' => 'Image',
            'through' => 'poa_files',
            'foreign_key' => 'del_rep_id',
            'far_key' => 'file_id'
        ],
        'devAppFiles' => [
            'model' => 'Image',
            'through' => 'dev_app_files',
            'foreign_key' => 'del_rep_id',
            'far_key' => 'file_id'
        ],
        'reserveMaterials' => [
            'model' => 'DReserveMaterial',
            'foreign_key' => 'report_id'
        ],
        'transferableItems' => [
            'model' => 'DTransferableItem',
            'foreign_key' => 'report_id'
        ]
    ];

    /**
     * Перегрузка метода инициализации
     * Добавляем информацию о тех кто работал с текущей записью
     */
    protected function _initialize(){
        if(Auth::instance()->get_user()){
            $this->_created_by_column = ['column' => 'created_by', 'value' => Auth::instance()->get_user()->id];
        }else{
            $this->_created_by_column = ['column' => 'created_by'];
        }

        parent::_initialize();
    }

    public function davAppFilePath(){
        $dir = DOCROOT.'media/data/delivery-reports/'.$this->pk().'/devapp';
        if( ! is_dir($dir)){
            mkdir($dir,0777,true);
        }
        return $dir;
    }

    public function poaFilePath(){
        $dir = DOCROOT.'media/data/delivery-reports/'.$this->pk().'/poa';
        if( ! is_dir($dir)){
            mkdir($dir,0777,true);
        }
        return $dir;
    }

    public function stdFilePath(){
        $dir = DOCROOT.'media/data/delivery-reports/'.$this->pk();
        if( ! is_dir($dir)){
            mkdir($dir,0777,true);
        }
        return $dir;
    }
}
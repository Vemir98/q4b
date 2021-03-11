<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 20.11.2017
 * Time: 12:40
 */
class Model_PlanTracking extends MORM
{
    protected $_table_name = 'plan_tracking';

    protected $_created_column = ['column' => 'created_at', 'format' => true];
    protected $_updated_column = ['column' => 'updated_at', 'format' => true];

    protected $_has_many = [
        'plans' => [
            'model' => 'PrPlan',
            'foreign_key' => 'tracking_id',
            'far_key' => 'plan_id',
            'through' => 'plans_trackings'
        ]
    ];

    protected $_belongs_to = [
        'creator' => [
            'model' => 'User',
            'foreign_key' => 'created_by'
        ],
        'updater' => [
            'model' => 'User',
            'foreign_key' => 'updated_by'
        ],
        'project' => [
            'model' => 'Project',
            'foreign_key' => 'project_id'
        ]
    ];

    /**
     * Переопределение метода инициализации
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

    public static function getPreResultItems($projectId,$profession = 0){
        $join = '';
        if( $profession){
            $join = 'INNER JOIN plans_trackings pts ON plantracking.id = pts.tracking_id JOIN pr_plans pp ON pts.plan_id = pp.id WHERE pp.profession_id = '.$profession.' AND';
        }else{
            $join = 'WHERE';
        }
        return DB::query(Database::SELECT,"SELECT id, recipient, TRIM(CONCAT_WS('',plan_names,plan_custom_names)) AS plan_names FROM 
(SELECT 
  pt.id,
  pt.recipient,
  (SELECT DISTINCT GROUP_CONCAT(' ',pp.name) FROM plan_tracking pt1 INNER JOIN plans_trackings pt2 ON pt1.id = pt2.tracking_id INNER JOIN pr_plans pp ON pt2.plan_id = pp.id WHERE pt1.id = pt.id) AS plan_names,
  (SELECT DISTINCT GROUP_CONCAT(' ',f.original_name) FROM plan_tracking pt1 INNER JOIN plans_trackings pt2 ON pt1.id = pt2.tracking_id INNER JOIN pr_plans pp ON pt2.plan_id = pp.id INNER JOIN pr_plans_files ppf ON pp.id = ppf.plan_id INNER JOIN files f ON ppf.file_id = f.id WHERE pt1.id = pt.id AND (pp.name IS NULL OR pp.name ='')) AS plan_custom_names
  FROM plan_tracking pt ".$join." pt.project_id = ".$projectId."
  ) AS pt")->execute()->as_array();
    }

    public function filePath(){
        if(strpos($this->file,'fs.qforb.net') === false){
            return '/'. $this->file;
        }
        return $this->file;
    }
}
<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Element extends ORM
{
    protected $_table_name = 'elements';

    protected $_belongs_to = [
        'company' => [
            'model' => 'Company',
            'foreign_key' => 'company_id'
        ],
        'project' => [
            'model' => 'Project',
            'foreign_key' => 'project_id'
        ]
    ];

    protected $_has_many = [
        'crafts' => [
            'model' => 'CmpCraft',
            'foreign_key' => 'element_id',
            'far_key' => 'craft_id',
            'through' => 'elements_cmp_crafts'
        ],
        'labTests' => [
            'model' => 'LabTest',
            'foreign_key' => 'element_id'
        ]
    ];

    public function getTableName(){
        return $this->_table_name;
    }

    public static function getElementCraftsIdsKeyValPairArray(){
        $output = [];
        $ec = DB::query(Database::SELECT,'SELECT tc.element_id pid, craft_id cid FROM elements_cmp_crafts tc 
                      INNER JOIN elements t ON tc.element_id = t.id')
            ->execute();

        if(!empty($ec)){
            foreach($ec as $el){
                $output[$el['pid']][] = $el['cid'];
            }
        }

        return $output;
    }
}
<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 07.12.2016
 * Time: 16:04
 */
class Model_Project extends MORM
{
    protected $_table_name = 'projects';

    protected $_created_column = ['column' => 'created_at', 'format' => true];
    protected $_updated_column = ['column' => 'updated_at', 'format' => true];

    protected $_belongs_to = [
        'client' => [
            'model' => 'Client',
            'foreign_key' => 'client_id'
        ],
        'company' => [
            'model' => 'Company',
            'foreign_key' => 'company_id'
        ],
        'main_image' => [
            'model' => 'Image',
            'foreign_key' => 'image_id'
        ]
    ];

    protected $_has_many = [
        'objects' => [
            'model' => 'PrObject',
            'foreign_key' => 'project_id'
        ],
        'users' => [
            'model' => 'User',
            'foreign_key' => 'project_id',
            'far_key' => 'user_id',
            'through' => 'users_projects'
        ],
        'tasks' => [
            'model' => 'PrTask',
            'foreign_key' => 'project_id'
        ],
        'plans' => [
            'model' => 'PrPlan',
            'foreign_key' => 'project_id'
        ],
        'certifications' => [
            'model' => 'PrCertification',
            'foreign_key' => 'project_id'
        ],
        'images' => [
            'model' => 'Image',
            'foreign_key' => 'project_id',
            'far_key' => 'file_id',
            'through' => 'projects_images'
        ],
        'links' => [
            'model' => 'Link',
            'foreign_key' => 'project_id',
            'far_key' => 'link_id',
            'through' => 'projects_links'
        ],
        'qc_professions' => [
            'model' => 'CmpProfession',
            'foreign_key' => 'project_id',
            'far_key' => 'profession_id',
            'through' => 'quality_controls'
        ],
        'quality_controls' => [
            'model' => 'QualityControl',
            'foreign_key' => 'project_id'
        ],
        'places' => [
            'model' => 'PrPlace',
            'foreign_key' => 'project_id'
        ],
        's_texts' => [
            'model' => 'STexts',
            'foreign_key' => 'project_id'
        ],
        'reserve_materials' => [
            'model' => 'ReserveMaterial',
            'foreign_key' => 'project_id'
        ],
        'transferable_items' => [
            'model' => 'TransferableItems',
            'foreign_key' => 'project_id'
        ],
    ];



    public function getTasksByModuleName($moduleName)
    {
        $moduleId = DB::query(Database::SELECT,"SELECT id FROM modules WHERE `name`='{$moduleName}'")->execute()->as_array()[0]['id'];

        $result = $this->tasks->join('pr_tasks_crafts')
            ->on('prtask.id', '=', 'pr_tasks_crafts.task_id')
                ->join('modules_tasks_crafts')
                ->on('pr_tasks_crafts.id', '=', 'modules_tasks_crafts.tc_id')
            ->where('modules_tasks_crafts.module_id', '=', $moduleId)->group_by('prtask.id');
        return $result;
    }
//
//    public function getTasks()
//    {
////        $moduleId = DB::query(Database::SELECT,"SELECT id FROM modules")->execute()->as_array()[0]['id'];
//
//        $result = $this->tasks->join('pr_tasks_crafts')
//            ->on('prtask.id', '=', 'pr_tasks_crafts.task_id')
//            ->join('modules_tasks_crafts')
//            ->on('pr_tasks_crafts.id', '=', 'modules_tasks_crafts.tc_id')
////            ->where('modules_tasks_crafts.module_id', '=', $moduleId)
//            ->group_by('prtask.id');
//        return $result;
//    }

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

    public function rules(){
        return [
            'name' => [
                ['not_empty'],
                ['max_length',[':value','50']],
            ],
            'address' => [
                ['not_empty'],
                ['max_length',[':value','250']],
            ],
            'description' => [
                ['max_length',[':value','250']],
            ],
            'status' => [
                ['not_empty'],
                [
                    function(Validation $valid){
                        if(empty($this->created_by)){
                            if($this->status != Enum_ProjectStatus::Active){
                                $valid->error('status', 'invalid_project_status');
                            }
                        }else{
                            if(!in_array($this->status,Enum_ProjectStatus::toArray())){
                                $valid->error('status', 'invalid_project_status');
                            }
                        }
                    },
                    [':validation']
                ],
            ],
            'project_id' => [
                ['max_length',[':value','32']],
            ],
            'owner' => [
                ['max_length',[':value','32']],
            ],
            'start_date' => [
                ['not_empty'],
                [
                    function(Validation $valid){
                        if($this->start_date <= 0){
                            $valid->error('start_date', 'incorrect_start_date');
                        }
                        if($this->start_date > $this->end_date){
                            $valid->error('start_date', 'start_date_greater_end_date');
                        }
                    },
                    [':validation']
                ],
            ],
            'end_date' => [
                ['not_empty'],
                [
                    function(Validation $valid){
                        if($this->end_date <= 0){
                            $valid->error('end_date', 'incorrect_end_date');
                        }
                        if($this->end_date < $this->start_date){
                            $valid->error('end_date', 'start_date_greater_end_date');
                        }
                    },
                    [':validation']
                ],
            ],
            'client_id' => [
                ['not_empty']
            ]
        ];
    }

    public function filters()
    {
        return [
            true => [
                ['htmlentities', [':value'],ENT_QUOTES],
                ['strip_tags']
            ]
        ];
    }

    public function create(Validation $validation = NULL){
        parent::create($validation);
        $tasksData = DB::query(Database::SELECT,'SELECT
              tasks.name,
              cmp_crafts.id AS craft_id,
              tasks.id AS task_id
            FROM tasks_crafts
              INNER JOIN tasks
                ON tasks_crafts.task_id = tasks.id
              INNER JOIN crafts
                ON tasks_crafts.craft_id = crafts.id
                AND crafts.status = \'enabled\'
              INNER JOIN cmp_crafts
                ON crafts.id = cmp_crafts.related_id
                AND cmp_crafts.status = \'enabled\'
                WHERE cmp_crafts.company_id = '.$this->company_id)
            ->execute()->as_array();

        if(count($tasksData)){
            $tasks = [];
            foreach ($tasksData as $td){
                if( ! isset($tasks[$td['name']])){
                    $tasks[$td['name']] = ORM::factory('PrTask');
                    $tasks[$td['name']]->name = $td['name'];
                    $tasks[$td['name']]->project_id = $this->pk();
                    $tasks[$td['name']]->status = Enum_Status::Enabled;
                    $tasks[$td['name']]->save();
                }
            }

            foreach ($tasksData as $td){
                if(isset($tasks[$td['name']]) AND $tasks[$td['name']] instanceof ORM){
                    $tasks[$td['name']]->add('crafts',$td['craft_id']);
                }
            }
        }

        return $this;
    }

    public function filePath(){
        return DOCROOT.'media/data/projects/'.$this->id;
    }

    public function imagesPath(){
        return implode(DS,[$this->filePath(),'images']);
    }

    public function plansPath(){
        return implode(DS,[$this->filePath(),'plans']);
    }

    public function dateTrackingPath(){
        return implode(DS,[$this->filePath(),'dateTracking']);
    }

    public function certificationsPath(){
        return implode(DS,[$this->filePath(),'certifications']);
    }

    public function qualityControlPath(){
        return implode(DS,[$this->filePath(),'quality_control']);
    }
    public function labTestTicketsPath(){
        return implode(DS,[$this->filePath(),'labtest-tickets']);
    }
    public function makeProjectPaths(){
        $directories = [$this->filePath(),$this->imagesPath(),$this->plansPath(),$this->certificationsPath(),$this->qualityControlPath(),$this->dateTrackingPath(), $this->labTestTicketsPath()];
        foreach ($directories as $dir){
            if( ! is_dir($dir)){
                mkdir($dir,0777,true);
            }
        }
    }

    /**
     * Возвращает массив [pagination => Pagination, items => [Company,Company,...]]
     * @return array
     */
    public function findAllWithPagination(){
        $object = clone($this);
        $count = $object->count_all();
        $params = array_diff(Arr::merge(Request::current()->param(),['page' => '']),array(''));
        $pagination = Pagination::factory(array(
                'total_items'    => $count,
                'items_per_page' => 12,
                'view'              => 'pagination/project',
            )
        )
            ->route_params($params);

        return ['pagination' => $pagination, 'items' => $this->limit($pagination->items_per_page)->offset($pagination->offset)->find_all(),'total_items' => $count];
    }

    public static function getProjectsWithoutPlansSpecialities($projectIds = null){
        $output = [];
        if(is_array($projectIds)){
            if(!empty($projectIds)){
                $projectIds = 'AND pp.project_id IN('.implode(',',$projectIds).') ';
            }else{
                return $output;
            }

        }
        $result = DB::query(Database::SELECT,'SELECT p.id, COUNT(p.id) count FROM pr_plans pp
LEFT JOIN projects p ON pp.project_id = p.id
JOIN companies c ON p.company_id = c.id
LEFT JOIN pr_plans_cmp_crafts ppcc ON pp.id = ppcc.plan_id
LEFT JOIN quality_controls qc ON pp.id = qc.plan_id
WHERE ppcc.craft_id IS NULL
'.$projectIds.'
AND qc.id IS NULL
GROUP BY p.id')->execute();




        if(count($result)){
            foreach ($result as $i){
                $output[$i['id']] = $i['count'];
            }
        }
        return $output;
    }

    public function getObjectsBiggerAndSmallerFloors(){
        $output = ['min' => null,'max' => null];
        if( ! $this->id) return $output;
        $result = DB::query(Database::SELECT,'SELECT MIN(smaller_floor) min, MAX(bigger_floor) max FROM pr_objects po WHERE po.project_id = '.$this->id)->execute();
        if(count($result)){
            $output = $result[0];
        }
        return $output;

    }

    public function usedTasks($id = NULL){
        $and = '';
        if(!$id == NULL){
            $and = 'AND quality_controls.place_id = '.$id;
        }
        $tasks = DB::query(Database::SELECT,'SELECT
  pr_tasks.*,
  quality_controls.created_at created_at,
  users.email created_by
FROM qcontrol_pr_tasks
  INNER JOIN quality_controls
    ON qcontrol_pr_tasks.qcontrol_id = quality_controls.id
  INNER JOIN pr_tasks
    ON qcontrol_pr_tasks.task_id = pr_tasks.id
  INNER JOIN users
    ON quality_controls.created_by = users.id
   '.$and.'
    AND quality_controls.place_id = '.$id.'
GROUP BY pr_tasks.id
ORDER BY quality_controls.created_at DESC')->execute()->as_array();
        $newTasks = $ids = [];
        if(count($tasks)){
            foreach ($tasks as $key => $task){
                $tasks[$key]['crafts'] = [];
            }

            foreach ($tasks as $task){
                $ids []= $task['id'];
                $newTasks[$task['id']] = $task;
            }

            $cc = DB::query(Database::SELECT,'SELECT  qcontrol_pr_tasks.task_id,
  quality_controls.craft_id,
  CONCAT(qcontrol_pr_tasks.task_id,quality_controls.craft_id) cc
  FROM qcontrol_pr_tasks
  INNER JOIN quality_controls
    ON qcontrol_pr_tasks.qcontrol_id = quality_controls.id
 WHERE qcontrol_pr_tasks.task_id IN('.implode(',',$ids).')
 GROUP BY (cc)')->execute()->as_array();

            if(count($cc)){
                foreach ($cc as $c){
                    $newTasks[$c['task_id']]['crafts'][] = $c['craft_id'];
                }
            }
        }
        return json_decode(json_encode($newTasks));
    }
}
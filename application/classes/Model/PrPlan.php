<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 09.03.2017
 * Time: 0:43
 */
class Model_PrPlan extends MORM
{
    protected $_table_name = 'pr_plans';

    protected $_created_column = ['column' => 'created_at', 'format' => true];
    protected $_updated_column = ['column' => 'updated_at', 'format' => true];

    protected $_has_many = [
        'files' => [
            'model' => 'PlanFile',
            'through' => 'pr_plans_files',
            'foreign_key' => 'plan_id',
            'far_key' => 'file_id'
        ],
        'extra_files' => [
            'model' => 'PlanExtraFile',
            'through' => 'pr_plans_extra_files',
            'foreign_key' => 'plan_id',
            'far_key' => 'file_id'
        ],
        'floors' => [
            'model' => 'PrFloor',
            'through' => 'pr_floors_pr_plans',
            'foreign_key' => 'plan_id',
            'far_key' => 'floor_id'
        ],
        'quality_controls' => [
            'model' => 'QualityControl',
            'foreign_key' => 'plan_id',
        ],
        'trackings' => [
            'model' => 'PlanTracking',
            'foreign_key' => 'plan_id',
            'far_key' => 'tracking_id',
            'through' => 'plans_trackings'
        ],
        'crafts' => [
            'model' => 'CmpCraft',
            'through' => 'pr_plans_cmp_crafts',
            'foreign_key' => 'plan_id',
            'far_key' => 'craft_id'
        ],
    ];

    protected $_belongs_to = [
        'profession' => [
            'model' => 'CmpProfession',
            'foreign_key' => 'profession_id',
        ],
        'place' => [
            'model' => 'PrPlace',
            'foreign_key' => 'place_id',
        ],
        'object' => [
            'model' => 'PrObject',
            'foreign_key' => 'object_id',
        ],
        'project' => [
            'model' => 'Project',
            'foreign_key' => 'project_id',
        ],
        'creator' => [
            'model' => 'User',
            'foreign_key' => 'created_by',
        ],
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

    public static function getNewScope(){
        $res = DB::query(Database::SELECT,'SELECT MAX(scope) + 1 as scp FROM pr_plans')->execute()->as_array();
        return $res[0]['scp'];
    }

    public function getFloorsAsString(){
        $floors = $this->floors->order_by('number','ASC')->find_all();
        $floorNumbers = [];

        foreach ($floors as $floor){
            $floorNumbers []= $floor->number;
        }
        return self::getNumbersRangeString($floorNumbers);
    }

    public static function getNumbersRangeString(array $numbers = null){

        //$numbers = [1,53,2,3,4,5,8,10,11,12,13,14,17,18,31,36,45,47,49];
        sort($numbers,SORT_NUMERIC);
        $tmp = [];
        $output = [];
        $i = 0;
        foreach( $numbers as $value ){
            if(!isset($tmp[$i])){
                $tmp[$i][] = $value;
            }else{
                if($value - $tmp[$i][count($tmp[$i])-1] > 1){
                    $tmp[++$i][] = $value;
                }else{//=1
                    $tmp[$i][] = $value;
                }
            }

        }
        if(!empty($tmp)){
            foreach($tmp as $arr){
                if(count($arr) > 2){
                    $result = '';
                    if($arr[0] < 0){
                        $result .= '<span class="bidi-override">('.$arr[0].')&lrm;</span>-<span class="bidi-override">('.$arr[count($arr)-1].')&lrm;</span>';
                    }else{
                        $result .= $arr[0].'-'.$arr[count($arr)-1];
                    }

                    $output[] = $result;
                }elseif(count($arr) > 1){
                    $output[]='<span class="bidi-override">'.$arr[0].'</span>,<span class="bidi-override">'.$arr[1].'</span>';
                }else{
                    $output[] = '<span class="bidi-override">'.$arr[0].'</span>';
                }
            }
        }

        return implode(',',$output);
    }
    
    public function floorIds(){
        $floors = $this->floors->find_all();
        $ids = [];
        foreach($floors as $floor){
            $ids []= $floor->id;
        }
        
        return $ids;
    }

    public function floorNumbers(){
        $floors = $this->floors->find_all();
        $numbers = [];
        foreach($floors as $floor){
            $numbers []= $floor->number;
        }

        return $numbers;
    }

    public function cloneIntoObject(Model_PrObject $object){
        $plan = ORM::factory('PrPlan');
        $plan->values($this->as_array(),['name','date','scale']);
        $plan->project_id = $object->project_id;
        $plan->object_id = $object->id;
        $plan->scope = self::getNewScope();

        if($this->place_id){
            $place = $object->places->where('number','=',$this->place->number)->find();
            if($place->loaded()){
                $plan->place_id = $place->id;
            }
        }

        $copyPlanProfession = $object->project->qc_professions->where('name','=',$this->profession->name)->find();

        if(!$copyPlanProfession->loaded()) return false;
//$copyPlanProfession = $object->project->qc_professions->where('catalog_number','like','%'. $this->catalog_number .'%')->find_all();
//echo "<pre>";
//print_r($copyPlanProfession);
//echo "</pre>";
//echo "<pre>";
//print_r('aaa');
//echo "</pre>";
//die;
        $plan->profession_id = $copyPlanProfession->id;

//        if($this->file()->loaded()){ //Plan name is file name
//            $name = $this->file()->getName();
//        }else{
//            $name = $this->name;
//        }
//        $plan->name = trim($name);

        $plan->save();
        $floors = $this->floors->find_all();
        if(count($floors)){
            foreach ($floors as $floor){
                $plan->add('floors',$floor->id);
            }
        }

//        $files = $this->files->find_all();
//        foreach($files as $file){
//            $newFile = ORM::factory('PlanFile')->values(Arr::extract($file->as_array(),['name','original_name','mime','ext','path','status']));
//            $newFile->name = uniqid().'.'.$newFile->ext;
//            $newFile->token = md5($newFile->name).base_convert(microtime(false), 10, 36);
//            $newFile->save();
//            if (!copy($file->fullFilePath(), $newFile->fullFilePath())) {
//                throw new HDVP_Exception('Error while copy file');
//            }
//            $plan->add('files',$newFile->id);
//        }
//echo "<pre>";
//print_r($plan->id);
//echo "</pre>";

        $fileData = [
            'sheet_number' => null,
            'path' => str_replace(DOCROOT,'',$object->project->plansPath()),
        ];

        $file = ORM::factory('PlanFile')->values($fileData)->save();
        $plan->add('files', $file->pk());
        $file->customName($this->file()->getName());


        return $plan;
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

    public function rules(){
        return [
            'project_id' => [
                ['not_empty'],
                ['numeric'],
            ],
            'object_id' => [
                ['not_empty'],
                ['numeric'],
            ],
            'profession_id' => [
                ['not_empty'],
                ['numeric'],
            ],
        ];
    }

    public function hasQualityControl(){
        return (bool) $this->quality_controls->count_all();
    }

    public function hasFile(){
        return (bool) ($this->file()->loaded() and $this->has_file);
    }

    public function file(){
        return $this->files->order_by('id','DESC')->find();
    }

    public function getProfession(){
        return $this->profession;
    }

    public static function getPreResultItems($project_id){
       return DB::query(Database::SELECT,'SELECT pp.id, pp.name, f.original_name, fcn.name AS cname, pp1.custom_number
  FROM pr_plans pp 
  JOIN pr_plans_files ppf ON pp.id = ppf.plan_id 
  JOIN files f ON ppf.file_id = f.id 
  LEFT JOIN files_custom_names fcn ON f.id = fcn.file_id 
  LEFT JOIN pr_places pp1 ON pp.place_id = pp1.id
  WHERE pp.project_id = '.(int)$project_id)->execute()->as_array();
    }

    public function isDeliveredAndReceived()
    {
        return (bool) ($this->delivered_at and $this->received_at);
    }
}
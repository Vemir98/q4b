<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 08.12.2016
 * Time: 3:27
 */
class Model_PrObject extends MORM
{
    protected $_table_name = 'pr_objects';

    protected $_created_column = ['column' => 'created_at', 'format' => true];
    protected $_updated_column = ['column' => 'updated_at', 'format' => true];

    protected $_belongs_to = [
        'project' => [
            'model' => 'Project',
            'foreign_key' => 'project_id'
        ],
        'type' => [
            'model' => 'PrObjectType',
            'foreign_key' => 'type_id'
        ]
    ];


    protected $_has_many = [
        'floors' => [
            'model' => 'PrFloor',
            'foreign_key' => 'object_id'
        ],
        'places' => [
            'model' => 'PrPlace',
            'foreign_key' => 'object_id'
        ],
        'plans' => [
            'model' => 'PrPlan',
            'foreign_key' => 'object_id'
        ],
        'quality_controls' => [
            'model' => 'QualityControl',
            'foreign_key' => 'object_id'
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

    public function rules(){
        return [
            'name' => [
                ['max_length',[':value','50']],
            ],
            'type_id' => [
                ['not_empty'],
                ['numeric']
            ],
            'floors' => [
                ['numeric']
            ],
            'places' => [
                ['numeric'],
                [
                    function(Validation $valid){
                        if((($this->getFloorsCount() > $this->places_count) OR $this->places_count < 0 ) AND $this->pk()){
                            $valid->error('places_count', 'incorrect_places_count');
                        }
                    },
                    [':validation']
                ],
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

    public function getFloorsCount(){
        return self::floorsCountFromRange($this->smaller_floor, $this->bigger_floor);
    }

    public static function floorsCountFromRange($smaller,$bigger){
        return (($bigger >= 0) ? abs($bigger) : $bigger) + abs($smaller) + 1;
    }

//    public function renumberFloors($startFloor){
//        return DB::query(Database::UPDATE,"call renumberObjectFloors(8,$startFloor)")->execute();
//    }

    public function deleteAllFloors(){
        $res = DB::query(Database::DELETE,"DELETE pr_floors WHERE id=".$this->id."")->execute();
        $this->_updateBiggerAndSmallerFloors();
        $this->_updatePlacesCount();
        return $res;
    }
    
    public function deleteFloor($floor){
        $number = $floor->number;
        $projectId = $floor->project_id;
        $objectId = $floor->object_id;
        $floorAllPlacesCount = $floor->places->count_all();
        $floorPrivatePlacesCount = $floor->places->where('type','=',Enum_ProjectPlaceType::PrivateS)->count_all();
        $floorPublicPlacesCount = $floor->places->where('type','=',Enum_ProjectPlaceType::PublicS)->count_all();
        $floorLastPlace = $floor->places->order_by('ordering',($floor->number < 0) ? 'ASC' :'DESC')->find();
        $floor->delete();
        if($number >= 0){
            DB::query(Database::UPDATE,"UPDATE {$floor->getTableName()} SET `number` = `number` - 1 WHERE `number` > {$number} AND `project_id` = {$projectId} AND `object_id` = {$objectId}")->execute();
            if($floorLastPlace->loaded()){
                DB::query(Database::UPDATE,"UPDATE pr_places SET `number` = `number` - {$floorPrivatePlacesCount} WHERE `ordering` > {$floorLastPlace->ordering} AND `project_id` = {$projectId} AND `object_id` = {$objectId} AND `type`='".Enum_ProjectPlaceType::PrivateS."'")->execute();
                DB::query(Database::UPDATE,"UPDATE pr_places SET `number` = `number` - {$floorPublicPlacesCount} WHERE `ordering` > {$floorLastPlace->ordering} AND `project_id` = {$projectId} AND `object_id` = {$objectId} AND `type`='".Enum_ProjectPlaceType::PublicS."'")->execute();
                DB::query(Database::UPDATE,"UPDATE pr_places SET `ordering` = `ordering` - {$floorAllPlacesCount} WHERE `ordering` > {$floorLastPlace->ordering} AND `project_id` = {$projectId} AND `object_id` = {$objectId}")->execute();
            }
        }else{
            DB::query(Database::UPDATE,"UPDATE {$floor->getTableName()} SET `number` = `number` + 1 WHERE `number` < {$number} AND `project_id` = {$projectId} AND `object_id` = {$objectId}")->execute();
            if($floorLastPlace->loaded()){
                DB::query(Database::UPDATE,"UPDATE pr_places SET `number` = `number` + ".($floorPrivatePlacesCount)." WHERE `ordering` < {$floorLastPlace->ordering} AND `project_id` = {$projectId} AND `object_id` = {$objectId} AND `type`='".Enum_ProjectPlaceType::PrivateS."'")->execute();
                DB::query(Database::UPDATE,"UPDATE pr_places SET `number` = `number` + ".($floorPublicPlacesCount)." WHERE `ordering` < {$floorLastPlace->ordering} AND `project_id` = {$projectId} AND `object_id` = {$objectId} AND `type`='".Enum_ProjectPlaceType::PublicS."'")->execute();
                DB::query(Database::UPDATE,"UPDATE pr_places SET  `ordering` = `ordering` - ".($floorAllPlacesCount)." WHERE `ordering` > ".($floorLastPlace->ordering)." AND `project_id` = {$projectId} AND `object_id` = {$objectId}")->execute();
            }
        }
        $this->_updateBiggerAndSmallerFloors();
        $this->_updatePlacesCount();
    }

    public function copyFloorToUp($floor){
        if($floor->number >= 0){
            DB::query(Database::UPDATE,"UPDATE {$floor->getTableName()} SET `number` = `number` + 1 WHERE `number` > {$floor->number} AND `project_id` = {$floor->project_id} AND `object_id` = {$floor->object_id}")->execute();

            $floorAllPlacesCount = $floor->places->count_all();
            $floorPrivatePlacesCount = $floor->places->where('type','=',Enum_ProjectPlaceType::PrivateS)->count_all();
            $floorPublicPlacesCount = $floor->places->where('type','=',Enum_ProjectPlaceType::PublicS)->count_all();
            $floorLastPlace = $floor->places->order_by('ordering',($floor->number < 0) ? 'ASC' :'DESC')->find();
            $floorPrivateLastPlace = $floor->places->where('type','=',Enum_ProjectPlaceType::PrivateS)->order_by('ordering',($floor->number < 0) ? 'ASC' :'DESC')->find();
            $floorPublicLastPlace = $floor->places->where('type','=',Enum_ProjectPlaceType::PublicS)->order_by('ordering',($floor->number < 0) ? 'ASC' :'DESC')->find();
            $ordering = $floorLastPlace->ordering + 1;
            $privateNumber = $floorPrivateLastPlace->number + 1;
            $publicNumber = $floorPublicLastPlace->number + 1;

            $newFloor = ORM::factory('PrFloor');
            $newFloor->project_id = $floor->project_id;
            $newFloor->object_id = $floor->object_id;
            $newFloor->number = $floor->number + 1;
            $newFloor->custom_name = $floor->custom_name;
            $newFloor->save();

            DB::query(Database::UPDATE,"UPDATE pr_places SET `number` = `number` + {$floorPrivatePlacesCount}  WHERE `ordering` > {$floorLastPlace->ordering} AND `project_id` = {$floor->project_id} AND `object_id` = {$floor->object_id} AND `type`='".Enum_ProjectPlaceType::PrivateS."'")->execute();
            DB::query(Database::UPDATE,"UPDATE pr_places SET `number` = `number` + {$floorPublicPlacesCount} WHERE `ordering` > {$floorLastPlace->ordering} AND `project_id` = {$floor->project_id} AND `object_id` = {$floor->object_id} AND `type`='".Enum_ProjectPlaceType::PublicS."'")->execute();
            DB::query(Database::UPDATE,"UPDATE pr_places SET  `ordering` = `ordering`  + ".($floorAllPlacesCount)." WHERE `ordering` > {$floorLastPlace->ordering} AND `project_id` = {$floor->project_id} AND `object_id` = {$floor->object_id}")->execute();

            foreach ($floor->places->order_by('ordering','ASC')->find_all() as $place){
                $newPlace = ORM::factory('PrPlace');
                $newPlace->project_id = $place->project_id;
                $newPlace->object_id = $place->object_id;
                $newPlace->floor_id = $newFloor->id;
                $newPlace->name = $place->name;
                $newPlace->type = $place->type;
                $newPlace->icon = $place->icon;
                $newPlace->number = ${$place->type.'Number'}++;
                $newPlace->custom_number = $place->custom_number;
                $newPlace->ordering = $ordering++;
                $newPlace->save();

                foreach ($place->spaces->find_all() as $space){
                    $newSpace = ORM::factory('PrSpace');
                    $newSpace->place_id = $newPlace->id;
                    $newSpace->type_id = $space->type_id;
                    $newSpace->desc = $space->desc;
                    $newSpace->save();
                }
            }
        }else{
            DB::query(Database::UPDATE,"UPDATE {$floor->getTableName()} SET `number` = `number` - 1 WHERE `number` < {$floor->number} AND `project_id` = {$floor->project_id} AND `object_id` = {$floor->object_id}")->execute();

            $floorAllPlacesCount = $floor->places->count_all();
            $floorPrivatePlacesCount = $floor->places->where('type','=',Enum_ProjectPlaceType::PrivateS)->count_all();
            $floorPublicPlacesCount = $floor->places->where('type','=',Enum_ProjectPlaceType::PublicS)->count_all();
            $floorLastPlace = $floor->places->order_by('ordering',($floor->number < 0) ? 'ASC' :'DESC')->find();
            $floorPrivateLastPlace = $floor->places->where('type','=',Enum_ProjectPlaceType::PrivateS)->order_by('ordering',($floor->number < 0) ? 'ASC' :'DESC')->find();
            $floorPublicLastPlace = $floor->places->where('type','=',Enum_ProjectPlaceType::PublicS)->order_by('ordering',($floor->number < 0) ? 'ASC' :'DESC')->find();
            $ordering = $floorLastPlace->ordering - 1 + $floorAllPlacesCount;


            $newFloor = ORM::factory('PrFloor');
            $newFloor->project_id = $floor->project_id;
            $newFloor->object_id = $floor->object_id;
            $newFloor->number = $floor->number - 1;
            $newFloor->custom_name = $floor->custom_name;
            $newFloor->save();


            $privateNumber = $floorPrivateLastPlace->number - 1;
            $publicNumber = $floorPublicLastPlace->number - 1;
            DB::query(Database::UPDATE,"UPDATE pr_places SET `number` = `number` - ".($floorPrivatePlacesCount)." WHERE `ordering` < {$floorLastPlace->ordering} AND `project_id` = {$floor->project_id} AND `object_id` = {$floor->object_id} AND `type`='".Enum_ProjectPlaceType::PrivateS."'")->execute();
            DB::query(Database::UPDATE,"UPDATE pr_places SET `number` = `number` - ".($floorPublicPlacesCount)." WHERE `ordering` < {$floorLastPlace->ordering} AND `project_id` = {$floor->project_id} AND `object_id` = {$floor->object_id} AND `type`='".Enum_ProjectPlaceType::PublicS."'")->execute();
            DB::query(Database::UPDATE,"UPDATE pr_places SET  `ordering` = `ordering` + ".($floorAllPlacesCount)." WHERE `ordering` >= ".($floorLastPlace->ordering)." AND `project_id` = {$floor->project_id} AND `object_id` = {$floor->object_id}")->execute();

            foreach ($floor->places->order_by('ordering','DESC')->find_all() as $place){
                $newPlace = ORM::factory('PrPlace');
                $newPlace->project_id = $place->project_id;
                $newPlace->object_id = $place->object_id;
                $newPlace->floor_id = $newFloor->id;
                $newPlace->name = $place->name;
                $newPlace->type = $place->type;
                $newPlace->icon = $place->icon;
                $newPlace->number = ${$place->type.'Number'}--;
                $newPlace->custom_number = $place->custom_number;
                $newPlace->ordering = $ordering--;
                $newPlace->save();

                foreach ($place->spaces->find_all() as $space){
                    $newSpace = ORM::factory('PrSpace');
                    $newSpace->place_id = $newPlace->id;
                    $newSpace->type_id = $space->type_id;
                    $newSpace->desc = $space->desc;
                    $newSpace->save();
                }
            }
        }
        $this->_updateBiggerAndSmallerFloors();
        $this->_updatePlacesCount();
        return $newFloor;
    }

    protected function _updateBiggerAndSmallerFloors(){
        DB::query(Database::UPDATE,'UPDATE pr_objects po SET  po.bigger_floor = (SELECT MAX(pf1.number) FROM pr_floors pf1 WHERE po.id=pf1.object_id),
          po.smaller_floor = (SELECT MIN(pf2.number) FROM pr_floors pf2 WHERE po.id=pf2.object_id)
          WHERE po.id = '.$this->id)->execute();
    }

    protected function _updatePlacesCount(){
        DB::query(Database::UPDATE,'UPDATE pr_objects po
          SET po.places_count = (SELECT COUNT(*) FROM pr_places pp WHERE pp.object_id = po.id)
          WHERE po.id = '.$this->id)->execute();
    }
    
    public function updateDynamicDataColumns(){
        $this->_updateBiggerAndSmallerFloors();
        $this->_updatePlacesCount();
    }

    public function copy()
    {
        $object = ORM::factory('PrObject');
        $object->values($this->as_array(),['project_id','type_id','name','smaller_floor','bigger_floor','places_count','start_date','end_date','state']);
        $object->name .= ' (copy)';
        $object->save();
        foreach($this->floors->find_all() as $floor){
            $floor->cloneIntoObject($object);
        }
        return $object;
    }
}
<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 08.12.2016
 * Time: 3:28
 */
class Model_PrFloor extends ORM
{
    protected $_table_name = 'pr_floors';

    protected $_belongs_to = [
        'project' => [
            'model' => 'Project',
            'foreign_key' => 'project_id'
        ],
        'object' => [
            'model' => 'PrObject',
            'foreign_key' => 'object_id'
        ]
    ];

    protected $_has_many = [
        'places' => [
            'model' => 'PrPlace',
            'foreign_key' => 'floor_id'
        ],
        'plans' => [
            'model' => 'PrPlan',
            'foreign_key' => 'floor_id',
            'far_key' => 'plan_id',
            'through' => 'pr_floors_pr_plans',
        ]
    ];

    public function getTableName(){
        return $this->_table_name;
    }

    public function placeTypeChanged($place){
        $oldPlaceType = ($place->type == Enum_ProjectPlaceType::PrivateS) ? Enum_ProjectPlaceType::PublicS : Enum_ProjectPlaceType::PrivateS;
        $newNumber = 0;
        if($place->number > 0){

            $needlePlace = $this->object->places->where('ordering','>',$place->ordering)->and_where('type','=',$place->type)->order_by('ordering','ASC')->limit(1)->find();
            $newNumber = $needlePlace->number;
            //если элемент последний
            if(!$needlePlace->loaded()){
                $needlePlace = $this->object->places->where('ordering','<',$place->ordering)->and_where('number','>',0)->and_where('type','=',$place->type)->order_by('ordering','DESC')->limit(1)->find();
                if($needlePlace->loaded()){
                    $newNumber = $needlePlace->number + 1;
                }else{
                    $newNumber = 1;
                }

            }
            DB::query(Database::UPDATE,"UPDATE {$place->getTableName()} SET `number` = `number` + 1 WHERE `ordering` > {$place->ordering} AND `project_id` = {$place->project_id} AND `object_id` = {$place->object_id} AND `type`='{$place->type}'")->execute();
            DB::query(Database::UPDATE,"UPDATE {$place->getTableName()} SET `number` = `number` - 1 WHERE `ordering` > {$place->ordering} AND `project_id` = {$place->project_id} AND `object_id` = {$place->object_id} AND `type`='{$oldPlaceType}'")->execute();
        }else{
            $needlePlace = $this->object->places->where('ordering','>',$place->ordering)->and_where('type','=',$place->type)->order_by('ordering','ASC')->limit(1)->find();

            if($needlePlace->loaded()){
                $newNumber--;
            }else{
                $newNumber = -1;
            }
            DB::query(Database::UPDATE,"UPDATE {$place->getTableName()} SET `number` = `number` - 1 WHERE `ordering` < {$place->ordering} AND `project_id` = {$place->project_id} AND `object_id` = {$place->object_id} AND `number` < 0 AND `type`='{$place->type}'")->execute();
            DB::query(Database::UPDATE,"UPDATE {$place->getTableName()} SET `number` = `number` + 1 WHERE `ordering` < {$place->ordering} AND `project_id` = {$place->project_id} AND `object_id` = {$place->object_id} AND `number` < 0 AND `type`='{$oldPlaceType}'")->execute();
        }

        $place->number = $newNumber;
        $place->save();
    }

    public function deletePlace($place){
            Database::instance()->begin();
            if($place->number > 0){
                DB::query(Database::UPDATE,"UPDATE {$place->getTableName()} SET `number` = `number` - 1 WHERE `ordering` > {$place->ordering} AND `project_id` = {$this->project_id} AND `object_id` = {$this->object_id} AND `type`='{$place->type}'")->execute();
                DB::query(Database::UPDATE,"UPDATE {$place->getTableName()} SET `ordering` = `ordering` - 1 WHERE `ordering` > {$place->ordering} AND `project_id` = {$this->project_id} AND `object_id` = {$this->object_id}")->execute();
            }else{
                DB::query(Database::UPDATE,"UPDATE {$place->getTableName()} SET `number` = `number` + 1 WHERE `ordering` < {$place->ordering} AND `project_id` = {$this->project_id} AND `object_id` = {$this->object_id} AND `type`='{$place->type}'")->execute();
                DB::query(Database::UPDATE,"UPDATE {$place->getTableName()} SET `ordering` = `ordering` - 1 WHERE `ordering` > {$place->ordering} AND `project_id` = {$this->project_id} AND `object_id` = {$this->object_id}")->execute();
            }
            $place->delete();
        $this->_updateParentPlacesCount();
        return true;
    }

    public function copyPlaceToUp($place){
         if($place->number > 0){
                DB::query(Database::UPDATE,"UPDATE {$place->getTableName()} SET `number` = `number` + 1 WHERE `ordering` > {$place->ordering} AND `project_id` = {$place->project_id} AND `object_id` = {$place->object_id} AND `type`='{$place->type}'")->execute();
                DB::query(Database::UPDATE,"UPDATE {$place->getTableName()} SET `ordering` = `ordering` + 1 WHERE `ordering` > {$place->ordering} AND `project_id` = {$place->project_id} AND `object_id` = {$place->object_id}")->execute();
                $newPlace = ORM::factory('PrPlace');
                $newPlace->project_id = $place->project_id;
                $newPlace->object_id = $place->object_id;
                $newPlace->icon = $place->icon;
                $newPlace->floor_id = $place->floor_id;
                $newPlace->name = $place->name;
                $newPlace->type = $place->type;
                $newPlace->number = $place->number + 1;
                $newPlace->custom_number = $place->custom_number;
                $newPlace->ordering = $place->ordering + 1;
                $newPlace->save();
            }else{
                DB::query(Database::UPDATE,"UPDATE {$place->getTableName()} SET `number` = `number` - 1 WHERE `ordering` < {$place->ordering} AND `project_id` = {$place->project_id} AND `object_id` = {$place->object_id} AND `type`='{$place->type}'")->execute();
                DB::query(Database::UPDATE,"UPDATE {$place->getTableName()} SET `ordering` = `ordering` + 1 WHERE `ordering` >= {$place->ordering} AND `project_id` = {$place->project_id} AND `object_id` = {$place->object_id}")->execute();


             $newPlace = ORM::factory('PrPlace');
                $newPlace->project_id = $place->project_id;
                $newPlace->object_id = $place->object_id;
                $newPlace->floor_id = $place->floor_id;
                $newPlace->icon = $place->icon;
                $newPlace->name = $place->name;
                $newPlace->type = $place->type;
                $newPlace->number = $place->number - 1;
                $newPlace->custom_number = $place->custom_number;
                $newPlace->ordering = $place->ordering;
                $newPlace->save();

            }





        foreach ($place->spaces->find_all() as $space){
            $newSpace = ORM::factory('PrSpace');
            $newSpace->place_id = $newPlace->id;
            $newSpace->type_id = $space->type_id;
            $newSpace->desc = $space->desc;
            $newSpace->save();
        }
        $this->_updateParentPlacesCount();
            return $newPlace;
    }

    public function addPlaceAfter($place,$values){
            $newPlace = ORM::factory('PrPlace');
            $newPlace->project_id = $place->project_id;
            $newPlace->object_id = $place->object_id;
            $newPlace->floor_id = $place->floor_id;
            $newPlace->type = $values['type'];
            $newPlace->name = $values['name'];
            $newPlace->icon = $values['icon'];
            if($place->number > 0){
                $needlePlace = ORM::factory('PrPlace')->where('object_id','=',$newPlace->object_id)->
                and_where('type','=',$newPlace->type)->and_where('number','>',0)->
                and_where('ordering','<=',$place->ordering)->order_by('ordering','DESC')->limit(1)->find();
                if( ! $needlePlace->loaded()){
                    $newPlace->number = 1;
                }else{
                    $newPlace->number = $needlePlace->number + 1;
                }

                $newPlace->ordering = $place->ordering + 1;
                DB::query(Database::UPDATE,"UPDATE {$place->getTableName()} SET `number` = `number` + 1 WHERE `ordering` > {$place->ordering} AND `project_id` = {$this->project_id} AND `object_id` = {$this->object_id} AND `type`='{$newPlace->type}'")->execute();
                DB::query(Database::UPDATE,"UPDATE {$place->getTableName()} SET  `ordering` = `ordering` + 1 WHERE `ordering` > {$place->ordering} AND `project_id` = {$this->project_id} AND `object_id` = {$this->object_id}")->execute();
            }else{

                $needlePlace = ORM::factory('PrPlace')->where('object_id','=',$newPlace->object_id)->
                and_where('type','=',$newPlace->type)->and_where('number','<',0)->
                and_where('ordering','>=',$place->ordering)->order_by('ordering','DESC')->limit(1)->find();
                if( ! $needlePlace->loaded()){
                    $newPlace->number = -1;
                }else{
                    $newPlace->number = $needlePlace->number - 1;
                }


                $newPlace->number = $place->number - 1;
                $newPlace->ordering = $place->ordering;
                DB::query(Database::UPDATE,"UPDATE {$place->getTableName()} SET `number` = `number` - 1 WHERE `ordering` < {$place->ordering} AND `project_id` = {$this->project_id} AND `object_id` = {$this->object_id} AND `type`='{$newPlace->type}'")->execute();
                DB::query(Database::UPDATE,"UPDATE {$place->getTableName()} SET `ordering` = `ordering` + 1 WHERE `ordering` >= {$place->ordering} AND `project_id` = {$this->project_id} AND `object_id` = {$this->object_id}")->execute();
            }

            $newPlace->save();

        $this->_updateParentPlacesCount();
        return $newPlace;
    }

    protected function _updateParentPlacesCount(){
        DB::query(Database::UPDATE,'UPDATE pr_objects po
          SET po.places_count = (SELECT COUNT(*) FROM pr_places pp WHERE pp.object_id = po.id)
          WHERE po.id = '.$this->object_id)->execute();
    }

    public function cloneIntoObject(Model_PrObject $object){
        $floor = ORM::factory('PrFloor');
        $floor->values($this->as_array(),['number']);
        $floor->project_id = $object->project_id;
        $floor->object_id = $object->id;
        $floor->save();
        foreach($this->places->find_all() as $place){
            $place->cloneIntoFloor($floor);
        }
        return $floor;
    }
}
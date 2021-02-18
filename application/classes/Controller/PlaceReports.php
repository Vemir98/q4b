<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 23.09.2019
 * Time: 12:45
 */

class Controller_PlaceReports extends HDVP_Controller_Template
{
    public function before()
    {
        parent::before();
        if ($this->auto_render === TRUE)
        {
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Reports'))->set_url('/reports/list'));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Place report'))->set_url('/reports/place'));
        }
    }

    public function action_index(){
        $this->template->content = $this->searchForm();
    }

    public function searchForm($hidden = false){
        $companies = $this->_user->availableCompanies();
        $items = [];
        foreach($companies as $comp){
            $items[$comp->id] = [
                'id' => $comp->id,
                'name' => $comp->name,
                'projects' => [],
                'crafts' => [],
                'status' => $comp->status
            ];

            if($this->_user->getRelevantRole('outspread') == Enum_UserOutspread::Project){
                $usrProjects = $this->_user->projects->find_all();
                $usrProjectsArr = [];
                foreach($usrProjects as $pr){
                    $usrProjectsArr [] = $pr->id;
                }

                foreach ($comp->projects->find_all() as $proj){
                    if(!in_array($proj->id,$usrProjectsArr)) continue;
                    $items[$comp->id]['projects'][$proj->id] = [
                        'id' => $proj->id,
                        'name' => $proj->name,
                        'status' => $proj->status,
                    ];
                }
            }else{
                foreach ($comp->projects->find_all() as $proj){
                    $items[$comp->id]['projects'][$proj->id] = [
                        'id' => $proj->id,
                        'name' => $proj->name,
                        'status' => $proj->status,
                    ];
                }
            }


            foreach($comp->crafts->where('status','=',Enum_Status::Enabled)->find_all() as $craft){
                $items[$comp->id]['crafts'][$craft->id] = [
                    'id' => $craft->id,
                    'name' => $craft->name
                ];
            }

            if(empty($items[$comp->id]['projects']) OR empty($items[$comp->id]['crafts'])){
                unset($items[$comp->id]);
            }
        }

        return View::make('reports/place-report/search-form',[
            'data' => json_encode($items),
            'items' => $items,
            'hidden' => $hidden,
        ]);
    }

    public function action_get_projects(){
        $this->auto_render(false);
        $id = $this->getUIntParamOrDie($this->request->param('id'));
        $comp = ORM::factory('Company',$id);
        $result = [];
        $projIds = [];
        if($this->_user->getRelevantRole('outspread') == Enum_UserOutspread::Project){
            $usrProjects = $this->_user->projects->find_all();
            $usrProjectsArr = [];
            foreach($usrProjects as $pr){
                $usrProjectsArr [] = $pr->id;
            }

            foreach ($comp->projects->find_all() as $proj){
                if(!in_array($proj->id,$usrProjectsArr)) continue;
                $result['projects'][] = [
                    'id' => $proj->id,
                    'name' => htmlspecialchars_decode($proj->name)
                ];
                $projIds[] = $proj->id;
            }
        }else{
            foreach ($comp->projects->find_all() as $proj){
                $result['projects'][] = [
                    'id' => $proj->id,
                    'name' => htmlspecialchars_decode($proj->name)
                ];
                $projIds[] = $proj->id;
            }
        }

        $objects = ORM::factory('PrObject')->where('project_id','IN',DB::expr("(".implode(",",$projIds).")"))->find_all();
        $objNames = [];
        foreach ($objects as $object){
            $result['objects'][] = [
                'id' => $object->id,
                'name' => $object->name,
                'projectId' => $object->project_id,
            ];
            $objNames[$object->id] = $object->name;
        }


        $crafts = ORM::factory('CmpCraft')->where('company_id','=',$id)->and_where('status','=',Enum_Status::Enabled)->find_all();

        foreach ($crafts as $craft){
            $result['crafts'][] = [
                'id' => $craft->id,
                'name' => $craft->name
            ];
        }



        $this->_responseData = $result;
    }

    public function action_show(){
        $this->auto_render = false;
        $data = Arr::extract($this->post(),['company','project','object','crafts','status']);
        $project = ORM::factory('Project',$data['project']);
        $object = null;
        $floors = [];
        $places = [];
        $craftIds = [];
        $crafts = [];
        $placeCrafts = [];
        $craftsForStats = [];
        $objectsData = [];

        $objects = $project->objects->find_all();

        foreach ($objects as $obj){
            $objectsData[$obj->id]['object'] = $obj;
            $objectsData[$obj->id]['qcs'] = ORM::factory('QualityControl')->where('object_id','=',$objectsData[$obj->id]['object']->id)->and_where('craft_id','IN',DB::expr('('.implode(',',$data['crafts']).')'))->find_all();

            foreach ($objectsData[$obj->id]['qcs'] as $qc){
                $objectsData[$obj->id]['floors'][] = $qc->floor_id;
                $objectsData[$obj->id]['craftIds'][] = $qc->craft_id;

                if($qc->status == $data['status'] OR !in_array($data['status'],Enum_QualityControlStatus::toArray()))
                    $objectsData[$obj->id]['places'] = $qc->place_id;


                if(! isset($placeCrafts[$qc->place_id]) or ! in_array($qc->craft_id, $placeCrafts[$qc->place_id])){
                    if($qc->status == $data['status'] OR !in_array($data['status'],Enum_QualityControlStatus::toArray()))
                        $objectsData[$obj->id]['placeCrafts'][$qc->place_id][] = $qc->craft_id;
                    $objectsData[$obj->id]['craftsForStats'][$qc->place_id.'.'.$qc->craft_id] = $qc->craft_id;
                }
            }

            foreach ($objectsData[$obj->id]['placeCrafts'] as $id => $pc){
                $objectsData[$obj->id]['placeCrafts'][$id] = implode(',',$objectsData[$obj->id]['placeCrafts'][$id]);
            }

            $objectsData[$obj->id]['stats'] = $this->getStatsByPlaces($objectsData[$obj->id]['craftsForStats'], $objectsData[$obj->id]['object']->places);
//            if( ! count($objectsData[$obj->id]['qcs'])){
//                $objectsData[$obj->id]['floors'] = [];
//            }else{
//                $objectsData[$obj->id]['floors'] = $objectsData[$obj->id]['object']->floors->where('id','IN',DB::expr('('.implode(',',$objectsData[$obj->id]['floors']).')'))->order_by('number','DESC')->with('places')->find_all();
//            }
            $objectsData[$obj->id]['floors'] = $objectsData[$obj->id]['object']->floors->order_by('number','DESC')->with('places')->find_all();

        }


        /*
        $qcs = ORM::factory('QualityControl')->where('object_id','=',$object)->and_where('craft_id','IN',DB::expr('('.implode(',',$data['crafts']).')'))->find_all();
        foreach ($qcs as $qc){
            $floors[] = $qc->floor_id;
            $craftIds[] = $qc->craft_id;

            if($qc->status == $data['status'] OR !in_array($data['status'],Enum_QualityControlStatus::toArray()))
                $places[] = $qc->place_id;


            if(! isset($placeCrafts[$qc->place_id]) or ! in_array($qc->craft_id, $placeCrafts[$qc->place_id])){
                if($qc->status == $data['status'] OR !in_array($data['status'],Enum_QualityControlStatus::toArray()))
                    $placeCrafts[$qc->place_id][] = $qc->craft_id;
                $craftsForStats[$qc->place_id.'.'.$qc->craft_id] = $qc->craft_id;
            }
        }

        foreach ($placeCrafts as $id => $pc){
            $placeCrafts[$id] = implode(',',$placeCrafts[$id]);
        }



        $stats = $this->getStatsByPlaces($craftsForStats, $object->places);

        $floors = $object->floors->where('id','IN',DB::expr('('.implode(',',$floors).')'))->order_by('number','DESC')->with('places')->find_all();
//*/
        foreach (ORM::factory('CmpCraft')->where('id','IN',DB::expr('('.implode(',',$data['crafts']).')'))->find_all() as $c){
            $crafts[$c->id] = $c->name;
        }

        $craftAVG = $this->_calcAVG($objectsData);
        $view = View::make('reports/place-report/places',
            [
                'item' => $objectsData[$data['object']]['object'],
                'itemFloors' => $objectsData[$data['object']]['floors'],
                'placeIds' => $objectsData[$data['object']]['places'],
                'crafts' => $crafts,
                'stats' => $objectsData[$data['object']]['stats'],
                'placeCrafts' => $objectsData[$data['object']]['placeCrafts'],
                'craftIds' => array_unique($objectsData[$data['object']]['craftIds']),
                'company' => ORM::factory('Company',$data['company']),
                'project' => ORM::factory('Project',$data['project']),
                'object' => $objectsData[$data['object']]['object'],
                'status' => in_array($data['status'],Enum_QualityControlStatus::toArray()) ? __($data['status']) : __('All'),
                'qcStatus' => isset($data['status']) ? $data['status'] : 'all',
                'objectsData' => $objectsData,
                'craftAVG' => $craftAVG
            ]
        )->render();
        $this->setResponseData('report',$view);
    }

    public function getStatsByPlaces(array $craftIds, $places)
    {
        $total = $places->count_all();
        $craftsArray = [];
        foreach ($craftIds as $cId){
            if(isset($craftsArray[$cId])){
                $craftsArray[$cId]['count']++;
            }else{
                $craftsArray[$cId]['id'] = $cId;
                $craftsArray[$cId]['count'] = 1;
            }
        }

        foreach ($craftsArray as $cId => $cArr){
            $craftsArray[$cId]['percent'] = $cArr['count'] / $total * 100;
        }

        return $craftsArray;
    }

    public function action_qc_list(){
        $this->_checkForAjaxOrDie();
        $id = (int) $this->request->param('id');
        $status = $this->request->param('status');
        $qcStatus = $this->request->param('qcStatus');
        $crafts = trim($this->request->param('crafts'));

        $place = ORM::factory('PrPlace',$id);
        if(!$place->loaded()){
            throw new HTTP_Exception_404();
        }

        if(!empty($crafts))
            $query = $place->quality_control->where('craft_id','IN',DB::expr('('.$crafts.')'));
        else
            $query = $place->quality_control;
        $selectedStatus = 'all';

        $qualityControls['statuses'] = ['all' =>'All'] + Enum_QualityControlApproveStatus::toArray();
        if($qcStatus != 'all'){
            $query->where('status','=',$qcStatus);
        }
        foreach ($qualityControls['statuses'] as $key => &$val){
            $tmpUrlParams = Request::current()->param();
            $tmpRequest = clone($query);
            $tmpUrlParams['status'] = $status;
            $tmpUrlParams['qcStatus'] = $qcStatus;
            if($key == 'all'){
                $tmpUrlParams['status'] = null;
                $output = [
                    'count' => $tmpRequest->count_all(),
                    'url' => Route::url('site.place.reports2',$tmpUrlParams,'https'),
                    'text' => $val
                ];
            }else{
                $tmpUrlParams['status'] = $val;
                $output = [
                    'count' => $tmpRequest->and_where('approval_status','=',$val)->count_all(),
                    'url' => Route::url('site.place.reports2',$tmpUrlParams,'https'),
                    'text' => $val
                ];
            }
            $val = $output;
        }


        if(!empty($status)){
            if($qcStatus == 'all')
                $query->where('approval_status','=',$status);
            else
                $query->and_where('approval_status','=',$status);
            $selectedStatus = $status;

        }
        

        $query->order_by('id','DESC');

        $items = $query->find_all();



        $content = View::make('reports/place-report/qc-list',[
            'items' => $items,
            'selectedStatus' => $selectedStatus,
            'filterData' => $qualityControls,
        ]);

        $this->setResponseData('modal',$content->render());
    }

    private function _calcAVG(array $objectsData)
    {
        $stats = [];
        foreach ($objectsData as $od){
            foreach ($od['stats'] as $s){
                if( ! isset($stats[$s['id']])){
                    $stats[$s['id']] = $s['percent'];
                }else{
                    $stats[$s['id']] += $s['percent'];
                }
            }
        }
        $cnt = count($objectsData);
        foreach ($stats as $key => $val){
            $stats[$key] = $val / $cnt;
        }
        return $stats;
    }
}
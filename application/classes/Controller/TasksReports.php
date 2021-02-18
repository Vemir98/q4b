<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 15.05.2019
 * Time: 11:25
 */

class Controller_TasksReports extends HDVP_Controller_Template
{
//    protected $_actions_perms = [
//        'index,details' => [
//            'GET' => 'read'
//        ],
//        'show' => [
//            'POST' => 'read',
//            'GET' => 'read'
//        ],
//        'save,get_objects' => [
//            'POST' => 'read',
//        ],
//    ];
    public function before()
    {
        parent::before();
        if ($this->auto_render === TRUE)
        {
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Reports'))->set_url('/reports/list'));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Tasks report'))->set_url('/reports/tasks'));
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

        return View::make('reports/tasks-report/search-form',[
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
        $floors = [];
        $floorIds = [];
        $objNames = [];
        foreach ($objects as $object){
            $result['objects'][] = [
                'id' => $object->id,
                'name' => $object->name,
                'projectId' => $object->project_id,
            ];
            $objNames[$object->id] = $object->name;
            foreach ($object->floors->find_all() as $floor){
                $result['floors'][] = [
                    'id' => $floor->id,
                    'name' => __("floor")." ".$floor->number." (".$object->name.")",
                    "objectId" => $object->id
                ];
                $floorIds[] = $floor->id;
            }
        }

        $places = ORM::factory('PrPlace')->where('floor_id','IN',DB::expr("(".implode(',',$floorIds).")"))->find_all();

        foreach ($places as $place){
            $result['places'][] = [
                'id' => $place->id,
                'name' => $place->name,
                'number' => $place->number,
                'customNumber' => $place->custom_number,
                'objectId' => $place->object_id,
                'floorId' => $place->floor_id,
                'floorName' => __("floor")." ".$place->floor->number,
                'objectName' => $objNames[$place->object_id]
            ];
        }



        $this->_responseData = $result;
    }

    public function action_show(){
        $this->auto_render = false;
        $data = Arr::extract($this->post(),['company','project','objects','floors','place']);
        $contentData = [];
        $company = ORM::factory('Company',(int)$data['company']);
        $project = $company->projects->where('id','=',(int)$data['project'])->find();
        if((int)$data['place']){
            $place = ORM::factory('PrPlace',(int)$data['place']);
            $floor = $place->floor;
            $object = $floor->object;
            $contentData['content'][] = $this->getData(null,null,null,$place->id);
            $contentData['title'] = __('Tasks report for place');
            $contentData['placeName'] = $place->name." ".$place->custom_number;
            $contentData['floorsNames'] =  __("floor")." ".$floor->number;
            $contentData['objectsNames'] =  $object->name;
        }elseif(count($data['floors'])){
            $query = count($data['floors']) > 1 ? ('('.implode(',',$data['floors']).')') : '('.$data['floors'][0].')';
            $contentData['title'] = __('Tasks report for floor');
            $contentData['placeName'] = '-';
            $contentData['floorsNames'] =  [];
            $contentData['objectsNames'] =  [];
            $floors = ORM::factory('PrFloor')->where('id','IN',DB::expr($query))->find_all();
            $floorsCount = count($floors);
            foreach($floors as $floor){
                if(count($data['objects']) > 1 OR !count($data['objects'])){
                    $floorName = __("floor")." ".$floor->number." (".$floor->object->name.")";
                    $contentData['content'][] = $this->getData(null,null,$floor->id,null,$floorName);
                    $contentData['floorsNames'][$floor->id] = $floorName;
                    $contentData['objectsNames'][$floor->object->id] = $floor->object->name;
                }else{
                    $contentData['content'][] = $this->getData(null,null,$floor->id, null, $floorsCount > 1 ? (__("floor")." ".$floor->number) : null);
                    if($floorsCount > 1){
                        $contentData['floorsNames'][] = __("floor")." ".$floor->number;
                    }else{
                        $contentData['floorsNames'] = __("floor")." ".$floor->number;
                    }

                    $contentData['objectsNames'] = $floor->object->name;
                }
            }
            if(is_array($contentData['floorsNames'])){
                $contentData['floorsNames'] = implode(', ',$contentData['floorsNames']);
            }
            if(is_array($contentData['objectsNames'])){
                $contentData['objectsNames'] = implode(', ',$contentData['objectsNames']);
            }
        }elseif (count($data['objects'])){
            $query = count($data['objects']) > 1 ? ('('.implode(',',$data['objects']).')') : '('.$data['objects'][0].')';
            $contentData['title'] = __('Tasks report for stucture');
            $contentData['placeName'] = '-';
            $contentData['floorsNames'] =  '-';
            $contentData['objectsNames'] =  [];
            foreach(ORM::factory('PrObject')->where('id','IN',DB::expr($query))->find_all() as $object){
                $contentData['objectsNames'][$object->id] = $object->name;
                if(count($data['objects']) > 1){
                    $contentData['content'][] = $this->getData(null,$object->id,null,null,$object->name);
                }else{
                    $contentData['content'][] = $this->getData(null,$object->id);
                }
            }
            if(is_array($contentData['objectsNames'])){
                $contentData['objectsNames'] = implode(', ',$contentData['objectsNames']);
            }
        }elseif (!!$data['project']){
            $contentData['title'] = __('Tasks for project');
            $contentData['placeName'] = '-';
            $contentData['floorsNames'] =  '-';
            $contentData['objectsNames'] =  '-';
            $contentData['content'][] = $this->getData($project->id);
        }

        $contentData['companyName'] = $company->name;
        $contentData['companyLogo'] = $company->logo;
        $contentData['projectName'] = $project->name;
        $this->setResponseData('report',View::make('reports/tasks-report/report',$contentData));
    }
//reports/tasks/task_details/<type>/<id>/<craft_id>/<task_id>
    public function action_details(){
        $this->auto_render = false;
        $type = $this->request->param('type');
        $id = $this->getUIntParamOrDie($this->request->param('id'));
        $placeType = $this->request->param('placeType');
        $craftId = $this->getUIntParamOrDie($this->request->param('craft_id'));
        $craft = ORM::factory('CmpCraft',$craftId);
        $query = '';
        if($type != 'place' AND !in_array($placeType,Enum_ProjectPlaceType::toArray())){
            throw new HTTP_Exception_404();
        }
        switch ($type){
            case 'place': {
                $query = 'WHERE qc.place_id = '.$id.' AND ptc.craft_id = '.$craftId;
                break;
            }
            case 'floor': {
                $query = 'WHERE qc.floor_id = '.$id.' AND ptc.craft_id = '.$craftId.' AND place_type="'.$placeType.'"';
                break;
            }
            case 'object': {
                $query = 'WHERE qc.object_id = '.$id.' AND ptc.craft_id = '.$craftId.' AND place_type="'.$placeType.'"';
                break;
            }
            case 'project': {
                $query = 'WHERE qc.project_id = '.$id.' AND ptc.craft_id = '.$craftId.' AND place_type="'.$placeType.'"';
                break;
            }
            default :{
                throw new HTTP_Exception_404;
            }
        }

        $tasks = DB::query(Database::SELECT,'SELECT pp.name placeName,pp.custom_number customNumber,pp.number, pp.type, pt.id taskId, u.name user, pt.name taskName, ptc.craft_id, qc.created_by, CONCAT(ptc.task_id,"-",qc.created_by) as flag FROM quality_controls qc 
  INNER JOIN qcontrol_pr_tasks qpt ON qc.id = qpt.qcontrol_id
  INNER JOIN pr_tasks pt ON qpt.task_id = pt.id
  INNER JOIN pr_tasks_crafts ptc ON ptc.task_id = pt.id
  INNER JOIN users u ON qc.created_by = u.id
  INNER JOIN pr_places pp ON qc.place_id = pp.id
  '.$query.'
  GROUP BY flag')->execute()->as_array();
        $output = [];
        foreach ($tasks as $task){
            if( ! isset($output[$task['taskId']])){
                $number = !empty($task['customNumber']) ? $task['customNumber'] : ($task['type'] == 'public'? 'PB' : 'N').$task['number'];
                $output[$task['taskId']] = [
                    'number' => $task['placeName']."<br>(".$number.")",
                    'taskId' => $task['taskId'],
                    'desc' => $task['taskName'],
                    'users' => [$task['user']]
                ];
            }else{
                $output[$task['taskId']]['users'][] = $task['user'];
            }
        }
        $this->setResponseData('details',View::make('reports/tasks-report/details-ajax',['items' => $output, 'craft' => $craft]));
    }

    public function action_places(){
        $this->auto_render = false;
        $type = $this->request->param('type');
        $id = $this->getUIntParamOrDie($this->request->param('id'));
        $placeType = $this->request->param('placeType');
        $craftId = $this->getUIntParamOrDie($this->request->param('craft_id'));
        $craft = ORM::factory('CmpCraft',$craftId);

        switch ($type){
            case 'floor': {
                $places = ORM::factory('PrPlace')->where('type','=',$placeType)->and_where('floor_id','=',$id)->order_by('number','ASC')->find_all();
                break;
            }
            case 'object': {
                $places = ORM::factory('PrPlace')->where('type','=',$placeType)->and_where('object_id','=',$id)->order_by('number','ASC')->find_all();
                break;
            }
            case 'project': {
                $places = ORM::factory('PrPlace')->where('type','=',$placeType)->and_where('project_id','=',$id)->order_by('number','ASC')->find_all();
                break;
            }
            default :{
                throw new HTTP_Exception_404;
            }
        }

        $this->setResponseData('details',View::make('reports/tasks-report/places-ajax',['items' => $places, 'craft' => $craft]));
    }

    public function action_property_item_quality_control_list(){
        $this->_checkForAjaxOrDie();
        $id = (int) $this->request->param('id');
        $craftId = (int) $this->request->param('craft_id');
        $status = $this->request->param('status');

        $place = ORM::factory('PrPlace',$id);
        if(!$place->loaded()){
            throw new HTTP_Exception_404();
        }

        $query = $place->quality_control;
        $query->where('craft_id','=',$craftId);
        $selectedStatus = 'all';

        $qualityControls['statuses'] = ['all' =>'All'] + Enum_QualityControlApproveStatus::toArray();
        foreach ($qualityControls['statuses'] as $key => &$val){
            $tmpUrlParams = Request::current()->param();
            $tmpRequest = clone($query);
            $tmpUrlParams['status'] = $status;
            if($key == 'all'){
                $tmpUrlParams['status'] = null;
                $output = [
                    'count' => $tmpRequest->count_all(),
                    'url' => Route::url('site.tasks.reports4',$tmpUrlParams,'https'),
                    'text' => $val
                ];
            }else{
                $tmpUrlParams['status'] = $val;
                $output = [
                    'count' => $tmpRequest->and_where('approval_status','=',$val)->count_all(),
                    'url' => Route::url('site.tasks.reports4',$tmpUrlParams,'https'),
                    'text' => $val
                ];
            }
            $val = $output;
        }




        if(!empty($status)){
            $query->and_where('approval_status','=',$status);
            $selectedStatus = $status;
        }
        $query->order_by('id','DESC');

        $items = $query->find_all();



        $content = View::make('projects/property/item/quality-controls-list',[
            'items' => $items,
            'selectedStatus' => $selectedStatus,
            'filterData' => $qualityControls,
        ]);

        $this->setResponseData('modal',$content->render());
    }

    function getData($projectId = null, $objectId = null, $floorId = null, $placeId = null, $title = null){
        $output = [];
        $viewInfo = [];
        $output['title'] = $title;
        if($placeId){
            $place = ORM::factory('PrPlace',$placeId);
            if(!$place->loaded()){
                throw new HTTP_Exception_404;
            }
            $project = $place->project;
            $company = $project->company;
            $crafts = $company->crafts->where('status','=',Enum_Status::Enabled)->find_all();

            $craftTotalTasksCnt = DB::query(Database::SELECT,'
            SELECT ptc.craft_id, COUNT(ptc.craft_id) cnt FROM pr_tasks_crafts ptc INNER JOIN pr_tasks pt ON ptc.task_id = pt.id WHERE pt.project_id='.$project->id.' AND pt.status = "enabled" GROUP BY ptc.craft_id
            ')->execute()->as_array('craft_id');

            $craftUsedTasksCnt = DB::query(Database::SELECT,'
             SELECT tbl1.craft_id, tbl1.task_id, COUNT(tbl1.craft_id) cnt FROM ( SELECT ptc.task_id,ptc.craft_id, CONCAT(ptc.task_id,"-",ptc.craft_id) as tc, qc.project_id
            FROM pr_tasks_crafts ptc
            INNER JOIN qcontrol_pr_tasks qpt ON ptc.task_id = qpt.task_id
            INNER JOIN quality_controls qc ON qpt.qcontrol_id=qc.id
            WHERE qc.place_id = '.$place->id.'
            GROUP BY tc) AS tbl1
  GROUP BY tbl1.craft_id
            ')->execute()->as_array('craft_id');

            foreach ($crafts as $c){
                $output['data'][$c->id] = [
                    'id' => $c->id,
                    'name' => $c->name,
                    'taskId' => $craftUsedTasksCnt[$c->id]['task_id'],
                    'total' => $craftTotalTasksCnt[$c->id]['cnt'],
                    'used' => $craftUsedTasksCnt[$c->id]['cnt']
                ];

                if(isset($craftTotalTasksCnt[$c->id]) AND isset($craftUsedTasksCnt[$c->id])){
                    $output['data'][$c->id]['percent'] = round($craftUsedTasksCnt[$c->id]['cnt'] * 100 / $craftTotalTasksCnt[$c->id]['cnt'],2);
                }else{
                    $output['data'][$c->id]['percent'] = 0;
                }
            }
            //var_dump($craftTotalTasksCnt,$craftUsedTasksCnt);
            $viewInfo['path'] = 'reports/tasks-report/place';
            $output['project'] = $project;
            $output['company'] = $company;
            $output['object'] = $place->object;
            $output['floor'] = $place->floor;
            $output['place'] = $place;
        }elseif ($floorId){
            $floor = ORM::factory('PrFloor',$floorId);
            if(!$floor->loaded()){
                throw new HTTP_Exception_404;
            }
            $placesCount = ['public' => $floor->places->where('type','=','public')->count_all(),'private' => $floor->places->where('type','=','private')->count_all()];
            $project = $floor->project;
            $company = $project->company;
            $crafts = $company->crafts->where('status','=',Enum_Status::Enabled)->find_all();

            $craftTotalTasksCnt = DB::query(Database::SELECT,'
            SELECT ptc.craft_id, COUNT(ptc.craft_id) cnt FROM pr_tasks_crafts ptc INNER JOIN pr_tasks pt ON ptc.task_id = pt.id WHERE pt.project_id='.$project->id.' AND pt.status = "enabled" GROUP BY ptc.craft_id
            ')->execute()->as_array('craft_id');

            $craftUsedTasksCnt['public'] = DB::query(Database::SELECT,'
             SELECT tbl1.craft_id, COUNT(tbl1.craft_id) cnt FROM ( SELECT ptc.task_id,ptc.craft_id, CONCAT(ptc.task_id,"-",ptc.craft_id) as tc, qc.project_id
            FROM pr_tasks_crafts ptc
            INNER JOIN qcontrol_pr_tasks qpt ON ptc.task_id = qpt.task_id
            INNER JOIN quality_controls qc ON qpt.qcontrol_id=qc.id
            WHERE qc.floor_id = '.$floor->id.'
            AND qc.place_type = "public"
            GROUP BY tc) AS tbl1
  GROUP BY tbl1.craft_id
            ')->execute()->as_array('craft_id');

            $craftUsedTasksCnt['private'] = DB::query(Database::SELECT,'
             SELECT tbl1.craft_id, COUNT(tbl1.craft_id) cnt FROM ( SELECT ptc.task_id,ptc.craft_id, CONCAT(ptc.task_id,"-",ptc.craft_id) as tc, qc.project_id
            FROM pr_tasks_crafts ptc
            INNER JOIN qcontrol_pr_tasks qpt ON ptc.task_id = qpt.task_id
            INNER JOIN quality_controls qc ON qpt.qcontrol_id=qc.id
            WHERE qc.floor_id = '.$floor->id.'
            AND qc.place_type = "private"
            GROUP BY tc) AS tbl1
  GROUP BY tbl1.craft_id
            ')->execute()->as_array('craft_id');
            $viewInfo['path'] = 'reports/tasks-report/floor';
            $output['project'] = $project;
            $output['company'] = $company;
            $output['object'] = $floor->object;
            $output['floor'] = $floor;
        }elseif ($objectId){
            $object = ORM::factory('PrObject',$objectId);
            if(!$object->loaded()){
                throw new HTTP_Exception_404;
            }
            $placesCount = ['public' => $object->places->where('type','=','public')->count_all(),'private' => $object->places->where('type','=','private')->count_all()];
            $project = $object->project;
            $company = $project->company;
            $crafts = $company->crafts->where('status','=',Enum_Status::Enabled)->find_all();

            $craftTotalTasksCnt = DB::query(Database::SELECT,'
            SELECT ptc.craft_id, COUNT(ptc.craft_id) cnt FROM pr_tasks_crafts ptc INNER JOIN pr_tasks pt ON ptc.task_id = pt.id WHERE pt.project_id='.$project->id.' AND pt.status = "enabled" GROUP BY ptc.craft_id
            ')->execute()->as_array('craft_id');

            $craftUsedTasksCnt['public'] = DB::query(Database::SELECT,'
             SELECT tbl1.craft_id, COUNT(tbl1.craft_id) cnt FROM ( SELECT ptc.task_id,ptc.craft_id, CONCAT(ptc.task_id,"-",ptc.craft_id) as tc, qc.project_id
            FROM pr_tasks_crafts ptc
            INNER JOIN qcontrol_pr_tasks qpt ON ptc.task_id = qpt.task_id
            INNER JOIN quality_controls qc ON qpt.qcontrol_id=qc.id
            WHERE qc.object_id = '.$object->id.'
            AND qc.place_type = "public"
            GROUP BY tc) AS tbl1
  GROUP BY tbl1.craft_id
            ')->execute()->as_array('craft_id');

            $craftUsedTasksCnt['private'] = DB::query(Database::SELECT,'
             SELECT tbl1.craft_id, COUNT(tbl1.craft_id) cnt FROM ( SELECT ptc.task_id,ptc.craft_id, CONCAT(ptc.task_id,"-",ptc.craft_id) as tc, qc.project_id
            FROM pr_tasks_crafts ptc
            INNER JOIN qcontrol_pr_tasks qpt ON ptc.task_id = qpt.task_id
            INNER JOIN quality_controls qc ON qpt.qcontrol_id=qc.id
            WHERE qc.object_id = '.$object->id.'
            AND qc.place_type = "private"
            GROUP BY tc) AS tbl1
  GROUP BY tbl1.craft_id
            ')->execute()->as_array('craft_id');
            $viewInfo['path'] = 'reports/tasks-report/object';
            $output['project'] = $project;
            $output['company'] = $company;
            $output['object'] = $object;
        }elseif ($projectId){
            $project = ORM::factory('Project',$projectId);
            if(!$project->loaded()){
                throw new HTTP_Exception_404;
            }
            $placesCount = ['public' => $project->places->where('type','=','public')->count_all(),'private' => $project->places->where('type','=','private')->count_all()];
            $company = $project->company;
            $crafts = $company->crafts->where('status','=',Enum_Status::Enabled)->find_all();

            $craftTotalTasksCnt = DB::query(Database::SELECT,'
            SELECT ptc.craft_id, COUNT(ptc.craft_id) cnt FROM pr_tasks_crafts ptc INNER JOIN pr_tasks pt ON ptc.task_id = pt.id WHERE pt.project_id='.$project->id.' AND pt.status = "enabled" GROUP BY ptc.craft_id
            ')->execute()->as_array('craft_id');

            $craftUsedTasksCnt['public'] = DB::query(Database::SELECT,'
             SELECT tbl1.craft_id, COUNT(tbl1.craft_id) cnt FROM ( SELECT ptc.task_id,ptc.craft_id, CONCAT(ptc.task_id,"-",ptc.craft_id) as tc, qc.project_id
            FROM pr_tasks_crafts ptc
            INNER JOIN qcontrol_pr_tasks qpt ON ptc.task_id = qpt.task_id
            INNER JOIN quality_controls qc ON qpt.qcontrol_id=qc.id
            WHERE qc.project_id = '.$project->id.'
            AND qc.place_type = "public"
            GROUP BY tc) AS tbl1
  GROUP BY tbl1.craft_id
            ')->execute()->as_array('craft_id');

            $craftUsedTasksCnt['private'] = DB::query(Database::SELECT,'
             SELECT tbl1.craft_id, COUNT(tbl1.craft_id) cnt FROM ( SELECT ptc.task_id,ptc.craft_id, CONCAT(ptc.task_id,"-",ptc.craft_id) as tc, qc.project_id
            FROM pr_tasks_crafts ptc
            INNER JOIN qcontrol_pr_tasks qpt ON ptc.task_id = qpt.task_id
            INNER JOIN quality_controls qc ON qpt.qcontrol_id=qc.id
            WHERE qc.project_id = '.$project->id.'
            AND qc.place_type = "private"
            GROUP BY tc) AS tbl1
  GROUP BY tbl1.craft_id
            ')->execute()->as_array('craft_id');
            $viewInfo['path'] = 'reports/tasks-report/project';
            $output['project'] = $project;
            $output['company'] = $company;

        }else{
            throw new HTTP_Exception_404;
        }


        if(!$placeId){
            foreach ($crafts as $c){
                if(isset($craftTotalTasksCnt[$c->id]) AND isset($craftUsedTasksCnt['public'][$c->id])){
                    $output['data']['public'][$c->id]['percent'] = round($craftUsedTasksCnt['public'][$c->id]['cnt'] * 100 / ($craftTotalTasksCnt[$c->id]['cnt'] * $placesCount['public']),2);
                    $output['data']['public'][$c->id] += [
                        'id' => $c->id,
                        'name' => $c->name,
                        'total' => $craftTotalTasksCnt[$c->id]['cnt'] * $placesCount['public'],
                        'used' => $craftUsedTasksCnt['public'][$c->id]['cnt']
                    ];
                }
                if(isset($craftTotalTasksCnt[$c->id]) AND isset($craftUsedTasksCnt['private'][$c->id])){
                    $output['data']['private'][$c->id]['percent'] = round($craftUsedTasksCnt['private'][$c->id]['cnt'] * 100 / ($craftTotalTasksCnt[$c->id]['cnt'] * $placesCount['private']),2);
                    $output['data']['private'][$c->id]+= [
                        'id' => $c->id,
                        'name' => $c->name,
                        'total' => $craftTotalTasksCnt[$c->id]['cnt'] * $placesCount['private'],
                        'used' => $craftUsedTasksCnt['private'][$c->id]['cnt']
                    ];
                }
            }
            usort($output['data']['public'],function($a,$b){
                if($a['percent'] == $b['percent']){
                    return 0;
                }

                return ($a['percent'] > $b['percent']) ? -1 : 1;
            });
            usort($output['data']['private'],function($a,$b){
                if($a['percent'] == $b['percent']){
                    return 0;
                }

                return ($a['percent'] > $b['percent']) ? -1 : 1;
            });
        }else{
            usort($output['data'],function($a,$b){
                if($a['percent'] == $b['percent']){
                    return 0;
                }

                return ($a['percent'] > $b['percent']) ? -1 : 1;
            });
        }

        return View::make($viewInfo['path'],$output);
    }

    public function action_guest_access(){
        $data = Arr::extract($_GET,['company','project','objects','floors','place']);
        $contentData = [];
        $company = ORM::factory('Company',(int)$data['company']);
        $project = $company->projects->where('id','=',(int)$data['project'])->find();
        if((int)$data['place']){
            $place = ORM::factory('PrPlace',(int)$data['place']);
            $floor = $place->floor;
            $object = $floor->object;
            $contentData['content'][] = $this->getData(null,null,null,$place->id);
            $contentData['title'] = __('Tasks report for place');
            $contentData['placeName'] = $place->name." ".$place->custom_number;
            $contentData['floorsNames'] =  __("floor")." ".$floor->number;
            $contentData['objectsNames'] =  $object->name;
        }elseif(count($data['floors'])){
            $query = count($data['floors']) > 1 ? ('('.implode(',',$data['floors']).')') : '('.$data['floors'][0].')';
            $contentData['title'] = __('Tasks report for floor');
            $contentData['placeName'] = '-';
            $contentData['floorsNames'] =  [];
            $contentData['objectsNames'] =  [];
            $floors = ORM::factory('PrFloor')->where('id','IN',DB::expr($query))->find_all();
            $floorsCount = count($floors);
            foreach($floors as $floor){
                if(count($data['objects']) > 1 OR !count($data['objects'])){
                    $floorName = __("floor")." ".$floor->number." (".$floor->object->name.")";
                    $contentData['content'][] = $this->getData(null,null,$floor->id,null,$floorName);
                    $contentData['floorsNames'][$floor->id] = $floorName;
                    $contentData['objectsNames'][$floor->object->id] = $floor->object->name;
                }else{
                    $contentData['content'][] = $this->getData(null,null,$floor->id, null, $floorsCount > 1 ? (__("floor")." ".$floor->number) : null);
                    if($floorsCount > 1){
                        $contentData['floorsNames'][] = __("floor")." ".$floor->number;
                    }else{
                        $contentData['floorsNames'] = __("floor")." ".$floor->number;
                    }

                    $contentData['objectsNames'] = $floor->object->name;
                }
            }
            if(is_array($contentData['floorsNames'])){
                $contentData['floorsNames'] = implode(', ',$contentData['floorsNames']);
            }
            if(is_array($contentData['objectsNames'])){
                $contentData['objectsNames'] = implode(', ',$contentData['objectsNames']);
            }
        }elseif (count($data['objects'])){
            $query = count($data['objects']) > 1 ? ('('.implode(',',$data['objects']).')') : '('.$data['objects'][0].')';
            $contentData['title'] = __('Tasks report for stucture');
            $contentData['placeName'] = '-';
            $contentData['floorsNames'] =  '-';
            $contentData['objectsNames'] =  [];
            foreach(ORM::factory('PrObject')->where('id','IN',DB::expr($query))->find_all() as $object){
                $contentData['objectsNames'][$object->id] = $object->name;
                if(count($data['objects']) > 1){
                    $contentData['content'][] = $this->getData(null,$object->id,null,null,$object->name);
                }else{
                    $contentData['content'][] = $this->getData(null,$object->id);
                }
            }
            if(is_array($contentData['objectsNames'])){
                $contentData['objectsNames'] = implode(', ',$contentData['objectsNames']);
            }
        }elseif (!!$data['project']){
            $contentData['title'] = __('Tasks for project');
            $contentData['placeName'] = '-';
            $contentData['floorsNames'] =  '-';
            $contentData['objectsNames'] =  '-';
            $contentData['content'][] = $this->getData($project->id);
        }

        $contentData['companyName'] = $company->name;
        $contentData['companyLogo'] = $company->logo;
        $contentData['projectName'] = $project->name;
        $this->template->content = View::make('reports/tasks-report/report',$contentData).'<style>.generate-reports-bookmark, .report-send-out, .report-project-desc, .loader_backdrop, .sidebar, .layout > header {
                display: none!important;
            }</style>';
    }
}
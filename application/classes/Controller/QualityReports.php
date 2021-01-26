<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 25.03.2019
 * Time: 13:26
 */
use JonnyW\PhantomJs\Client;
class Controller_QualityReports extends HDVP_Controller_Template
{
    protected $_actions_perms = [
        'index,one_project,get_projects,saved,remove,make_pdf,test,print' => [
            'GET' => 'read'
        ],
        'show,send_reports' => [
            'POST' => 'read',
            'GET' => 'read'
        ],
        'save,get_objects' => [
            'POST' => 'read',
        ],
    ];
    public function before()
    {
        parent::before();
        if ($this->auto_render === TRUE)
        {
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Reports'))->set_url('/reports/list'));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Quality Report'))->set_url('/reports/quality'));
        }
        if(Request::current()->action() == 'make_pdf'){
            $this->template = View::make('reports/quality/for-pdf-new');
        }
    }
    public function action_index(){
        $this->template->content = $this->searchForm();
    }

    public function action_multiple_projects(){
        $this->auto_render(false);
        $content = View::make('reports/quality/multiple-projects');
        $this->setResponseData('report',$content);
    }

    public function action_one_project(){
        $this->template->content = View::make('reports/quality/one-project');
    }

    public function action_show(){
        /**
         * QualityReport
         */
        $report = (new QualityReport(new ReportQuery($this->post())));
        $report->generate();

        $content = View::make('reports/quality/multiple-projects',['report' => $report]);
        $this->setResponseData('report',$content);
    }

    public function searchForm($hidden = false){
        $companies = $this->_user->availableCompanies();
        $avProjects = $items = [];
        foreach($companies as $comp){
            $avComp[$comp->id] = $comp->id;
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

        foreach ($items as $key => $val){
            $avComp[$key] = $key;
            if(!empty($val['projects'])){
                foreach ($val['projects'] as $pk => $pv){
                    $avProjects[$pk] = $pk;
                }
            }
        }

        sort($avProjects);

        return View::make('reports/quality/search-form',[
            'data' => json_encode($items),
            'items' => $items,
            'hidden' => $hidden,
            'savedReports' => View::make(
                'reports/quality/saved-reports-list',
                [
                    'reports' =>
                        ORM::factory('QualityReport')->where('is_hidden','=',0)->and_where('company_id','IN',DB::expr(count($avComp) > 1 ? '('.implode(',',$avComp).')' : '('.array_values($avComp)[0].')'))->order_by('created_at','DESC')->find_all(),
                    'projectIds' => $avProjects
                ]
            ),

        ]);
    }

    public function action_get_projects(){
        $this->auto_render(false);
        $id = $this->getUIntParamOrDie($this->request->param('id'));
        $comp = ORM::factory('Company',$id);

        $avProjects = $result = [];

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
            }
        }else{
            foreach ($comp->projects->find_all() as $proj){
                $result['projects'][] = [
                    'id' => $proj->id,
                    'name' => htmlspecialchars_decode($proj->name)
                ];
            }
        }

        $crafts = ORM::factory('CmpCraft')->where('company_id','=',$id)->and_where('status','=',Enum_Status::Enabled)->find_all();

        foreach ($crafts as $craft){
            $result['crafts'][] = [
                'id' => $craft->id,
                'name' => $craft->name
            ];
        }
        $result['savedReports'] = [];
        $tmpCompanies = $this->_user->availableCompanies();
        $companies = [];
        foreach ($tmpCompanies as $tc){
            $companies[$tc->id] = true;
        }
        foreach ($result['projects']as $p){
            $avProjects[$p['id']] = $p['id'];
        }

        //$result['savedReports'] = View::make('reports/quality/saved-reports-list',['reports' => ORM::factory('QualityReport')->where('company_id','=',$id)->order_by('created_at','DESC')->find_all()])->render();
        $result['savedReports'] = [];
        if(!empty($companies[$id])){
            foreach (ORM::factory('QualityReport')->where('company_id','=',$id)->and_where('is_hidden','=',0)->order_by('created_at','DESC')->find_all() as $rep){
                $rp = explode(',',$rep->projects);
                if(!is_array($rp)){
                    $rp[0] = $rp;
                }

                foreach ($rp as $r){
                    if(!in_array($r,$avProjects)){
                        continue 2;
                    }
                }


                $result['savedReports'][] = [
                    'id' => $rep->id,
                    'name' => $rep->name.' '.date('d/m/Y',$rep->created_at),
                    'projects' => strpos(',',$rep->projects) ? explode(',',$rep->projects) : [(int)$rep->projects],
                    'url' => URL::site('reports/quality/saved/'.$rep->id),
                    'deleteURL' => URL::site('reports/quality/remove/'.$rep->id)
                ];
            }
        }


        $this->_responseData = $result;
    }

    public function action_get_objects(){
        $this->auto_render(false);
        $projectID = Arr::get($this->post(),'projects');
        $result = [];

        $objects = ORM::factory('PrObject')->where('project_id','=',$projectID)->find_all();
        foreach ($objects as $object){
            $result[] = [
                'id' => $object->id,
                'name' => $object->name
            ];
        }

        $this->_responseData = $result;
    }

    public function action_save(){
        $this->auto_render(false);
        $data = Arr::extract($this->post(),['name','generalOpinions','json','is_hidden','images']);

        $stats = JSON::decode($data['json']);

        $report = (new QualityReport())->loadSavedReport($stats);
        $reportModel = ORM::factory('QualityReport');
        $reportModel->name = $data['name'];
        $reportModel->images = $data['images'];
        $reportModel->company_id = $report->getCompany()['id'];
        $reportModel->is_hidden = (int)$data['is_hidden'];
        $tmp = [];

        foreach ($report->getProjects() as $project){
            $tmp[] = $project['id'];
        }
        $reportModel->projects = implode(',',$tmp);

        foreach ($report->getObjects() as $object){
            $tmp[] = $object['id'];
        }
        $reportModel->objects = implode(',',$tmp);

        unset($tmp);


        foreach ($data['generalOpinions'] as $gop){
            if(count($stats['data']['projects']) > 1){
                $stats['data']['projects'][$gop['id']]['generalOpinion'] = $gop['text'];
            }else{
                $stats['data']['objects'][$gop['id']]['generalOpinion'] = $gop['text'];
            }
        }
        $reportModel->from = $report->getDateFrom();
        $reportModel->to = $report->getDateTo();
        $reportModel->created_at = time();
        $reportModel->created_by = $this->_user->id;
        $reportModel->json = JSON::encode($stats);
        $reportModel->save();
//        dd(URL::site('media/data/reports/pdf/'.$reportModel->pk().'.pdf'));
//        $this->_responseData = $data['generalOpinions'][0]['id'];
        if(!$reportModel->is_hidden){
            $this->setResponseData('savedReports',View::make('reports/quality/saved-reports-list',['reports' => ORM::factory('QualityReport')->where('is_hidden','=',0)->order_by('created_at','DESC')->find_all()]));
        }else{

            $this->_makePdf($reportModel);
            if($reportModel->name == 'print'){
                $this->setResponseData('url',URL::site('reports/quality/print/'.$reportModel->pk(),'https'));
            }else{
                $this->setResponseData('url','https://qforb.sunrisedvp.systems/media/data/reports/pdf/'.$reportModel->pk().'.pdf');
            }
        }


    }

    public function action_saved(){
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Saved Report')));
        $reportModel = ORM::factory('QualityReport',$this->getUIntParamOrDie($this->request->param('id')));
        if(!$reportModel->loaded()){
            throw new HTTP_Exception_404();
        }
        $report = (new QualityReport())->loadSavedReport(JSON::decode($reportModel->json));
        $isPhantom = false;
//        if($this->request->headers('Pjsbot76463') == '99642'){
//            $isPhantom = true;
//        }
//        if(Request::current()->client()->get_ip_env() == '80.76.110.10') {
        if($this->request->headers('Pjsbot76463') == '99642'){
            $isPhantom = true;
            View::bind_global('isPhantom',$isPhantom);
            //$this->template = View::make('reports/quality/for-pdf-new', ['report' => $report, 'isPhantom' => $isPhantom]);
            $this->template->rawContent = View::make('reports/quality/print', ['report' => $report, 'isPhantom' => $isPhantom, 'images' => !empty($reportModel->images) ? JSON::decode($reportModel->images) : null]);
        }else{
            $this->template->content = View::make('reports/quality/multiple-projects', ['report' => $report, 'isPhantom' => false, 'reportId' => $this->getUIntParamOrDie($this->request->param('id'))]);
        }
    }

    public function action_print(){
        $reportModel = ORM::factory('QualityReport',$this->getUIntParamOrDie($this->request->param('id')));
        if(!$reportModel->loaded()){
            throw new HTTP_Exception_404();
        }
        $report = (new QualityReport())->loadSavedReport(JSON::decode($reportModel->json));

        $this->template->rawContent = View::make('reports/quality/print', ['report' => $report, 'isPhantom' => false, 'reportId' => $this->getUIntParamOrDie($this->request->param('id')),'images' => !empty($reportModel->images) ? JSON::decode($reportModel->images) : null]);
    }

    public function action_remove(){
        $this->auto_render(false);
        ORM::factory('QualityReport',$this->getUIntParamOrDie($this->request->param('id')))->delete();
    }

    public function action_send_reports(){
        $this->_checkForAjaxOrDie();
        if($this->request->method() == Request::POST){
            $data = Arr::get($this->post(),'report');
            $data = Arr::extract(JSON::decode(urldecode(Arr::get($this->post(),'report'))),['name','generalOpinions','json','is_hidden']);

            $stats = JSON::decode($data['json']);

            $report = (new QualityReport())->loadSavedReport($stats);

            $reportModel = ORM::factory('QualityReport');
            $reportModel->name = $data['name'];
            $reportModel->company_id = $report->getCompany()['id'];
            $reportModel->is_hidden = 1;
            $reportModel->token = md5($data['json']).base_convert(microtime(false), 10, 36);
            $tmp = [];
            foreach ($report->getProjects() as $project){
                $tmp[] = $project['id'];
            }
            $reportModel->projects = implode(',',$tmp);

            foreach ($report->getObjects() as $object){
                $tmp[] = $object['id'];
            }
            $reportModel->objects = implode(',',$tmp);

            unset($tmp);


            foreach ($data['generalOpinions'] as $gop){
                if(count($stats['data']['projects']) > 1){
                    $stats['data']['projects'][$gop['id']]['generalOpinion'] = $gop['text'];
                }else{
                    $stats['data']['objects'][$gop['id']]['generalOpinion'] = $gop['text'];
                }
            }
            $reportModel->from = $report->getDateFrom();
            $reportModel->to = $report->getDateTo();
            $reportModel->created_at = time();
            $reportModel->created_by = $this->_user->id;
            $reportModel->json = JSON::encode($stats);
            $reportModel->save();



            $client = Client::getInstance();
            $client->getEngine()->setPath(DOCROOT.'phantomjs-2.1.1-linux-x86_64/bin/phantomjs');

//            $client->getEngine()->setPath('/home/qforbnet/www/phantomjs-2.1.1-linux-x86_64/bin/phantomjs');
//        $client->getEngine()->addOption('--ignore-ssl-errors=true');
            $client->getEngine()->addOption('--cookies-file=cook.txt');
            $width  = 1690;
            $height = 1160;
            $top    = 290;
            $left   = 215;


            $request = $client->getMessageFactory()->createCaptureRequest('https://qforb.sunrisedvp.systems', 'GET');
            $request->addHeader('Pjsbot76463', '99642');
            $request->setViewportSize(1920, $height);
            $request->setCaptureDimensions($width, $height, $top, $left);
            $response = $client->getMessageFactory()->createResponse();
            $client->send($request, $response);
            $headers = $response->getHeaders();
            /**
             * @see JonnyW\PhantomJs\Http\CaptureRequest
             **/
            $request = $client->getMessageFactory()->createCaptureRequest(URL::withLang('/reports/quality/saved/'.$reportModel->pk(),'en','https'), 'GET');
            $request->addHeader('Pjsbot76463', '99642');
            $request->setViewportSize(1920, $height);
            $request->setCaptureDimensions($width, $height, $top, $left);
            $request->setOutputFile(DOCROOT.'media/data/mailing/reports/quality/'.$reportModel->pk().'.png');

            /**
             * @see JonnyW\PhantomJs\Http\Response
             **/
            $response = $client->getMessageFactory()->createResponse();

            // Send the request
            $client->send($request, $response);



            $emailsList = [];
            foreach ($this->post() as $key => $value){
                if(strpos($key,'emails') !== false){
                    $emailsList[] = $value;
                }

            }


            if(!empty($emailsList)){
                foreach ($emailsList as $key => $email){
                    if(!Valid::email($email)){
                        unset($emailsList[$key]);
                    }
                }

                if(count($emailsList)){
                    Queue::enqueue('mailing','Job_Report_SendQualityReportsEmail',[
                        'emails' => $emailsList,
                        'subject' => 'Q4b qualiti report share email for project - ',
                        'report' => $reportModel->pk(),
                        'message' => trim(strip_tags(Arr::get($this->post(),'message'))),
                        'link' => URL::site('/reports/quality/saved/'.$reportModel->pk(),'https'),
                        'user' => ['name' => $this->_user->name, 'email' => $this->_user->email],
                        'view' => 'emails/report/quality-guest-access',
                        'lang' => Language::getCurrent()->iso2,
                        'company' => $report->getCompany()['name'],
                    ],\Carbon\Carbon::now()->addSeconds(30)->timestamp);
                }else{
                    $this->_setErrors('Empty Mail list');
                }

            }else{
                $this->_setErrors('Empty Mail list');
            }
        }else{
            $id = $this->request->param('id');
            $autocompleteMailiList = [];
            $items = [];
            if($id[0] == "p"){
                $projects = str_replace('p','',$id);
                $projects = explode('-',$projects);
                foreach ($projects as $project){
                    foreach (ORM::factory('Project',$project)->users->find_all() as $usr){
                        $autocompleteMailiList[$usr->email] = $usr->email;
                        $items[] = $usr;
                    }
                }
            }else{
                $reportModel = ORM::factory('QualityReport',$id);
                $report = (new QualityReport())->loadSavedReport(JSON::decode($reportModel->json));
                foreach ($report->getProjects() as $project){
                    foreach (ORM::factory('Project',$project['id'])->users->find_all() as $usr){
                        $autocompleteMailiList[$usr->email] = $usr->email;
                        $items[] = $usr;
                    }
                }
            }


            $role = ORM::factory('Role',['outspread' => Enum_RoleOutspread::Super]);
            foreach ($role->users->find_all() as $usr){
                $autocompleteMailiList[$usr->email] = $usr->email;
            }

            $this->setResponseData('modal',View::make('reports/send-reports',['items' =>$items,'autocompleteMailList' => $autocompleteMailiList,'secure_tkn' =>'']));
            $this->setResponseData('triggerEvent','sendReports');
        }
    }

    public function action_make_pdf(){
        $reportModel = ORM::factory('QualityReport',$this->getUIntParamOrDie($this->request->param('id')));
        if(!$reportModel->loaded()){
            throw new HTTP_Exception_404();
        }
        $report = (new QualityReport())->loadSavedReport(JSON::decode($reportModel->json));
        $this->template->report = $report;
    }

    public function action_test(){
        $this->auto_render(false);

//        $client = Client::getInstance();
//        $client->getEngine()->setPath('/home/qforbnet/www/phantomjs-2.1.1-linux-x86_64/bin/phantomjs');
////        $client->getEngine()->addOption('--ignore-ssl-errors=true');
//        $client->getEngine()->addOption('--cookies-file=cook.txt');
//        $width  = 1690;
//        $height = 1160;
//        $top    = 290;
//        $left   = 215;
//
//
//        $request = $client->getMessageFactory()->createCaptureRequest('https://qforb.net', 'GET');
//        $request->addHeader('Pjsbot76463', '99642');
//        $response = $client->getMessageFactory()->createResponse();
//        $client->send($request, $response);
//        $headers = $response->getHeaders();
//        /**
//         * @see JonnyW\PhantomJs\Http\CaptureRequest
//         **/
//        $request = $client->getMessageFactory()->createCaptureRequest('https://qforb.net/he/reports/quality/saved/88', 'GET');
//        $request->addHeader('Pjsbot76463', '99642');
//        $request->setOutputFile(DOCROOT.'tmp.jpg');
//        $request->setViewportSize(1920, $height);
//        $request->setCaptureDimensions($width, $height, $top, $left);
//
//        /**
//         * @see JonnyW\PhantomJs\Http\Response
//         **/
//        $response = $client->getMessageFactory()->createResponse();
//
//        // Send the request
//        $client->send($request, $response);
        $this->test1();
    }

    public function test1(){

        $client = Client::getInstance();
        $client->getEngine()->setPath(DOCROOT.'phantomjs-2.1.1-linux-x86_64/bin/phantomjs');

//        $client->getEngine()->setPath('/home/qforbnet/www/phantomjs-2.1.1-linux-x86_64/bin/phantomjs');
        $client->getEngine()->addOption('--cookies-file=cook.txt');
        /**
         * @see JonnyW\PhantomJs\Http\CaptureRequest
         **/
        $request = $client->getMessageFactory()->createPdfRequest('http://df.sunrisedvp.systems/index.html', 'GET');
        $request->addHeader('Pjsbot76463', '99642');
        $request->setOutputFile(DOCROOT.'tmp.pdf');
        $request->setFormat('A1');
//        $request->setOrientation('landscape');
        $request->setViewportSize(1920, 1690);
        $request->setPaperSize(1960, 1750);
        /**
         * @see JonnyW\PhantomJs\Http\Response
         **/
        $response = $client->getMessageFactory()->createResponse();

        // Send the request
        $client->send($request, $response);
    }

    private function _makePdf($report){
        $client = Client::getInstance();
        $client->getEngine()->setPath(DOCROOT.'phantomjs-2.1.1-linux-x86_64/bin/phantomjs');
//        $client->getEngine()->setPath('/home/qforbnet/www/phantomjs-2.1.1-linux-x86_64/bin/phantomjs');
        $client->getEngine()->addOption('--cookies-file=cook.txt');


        $request = $client->getMessageFactory()->createCaptureRequest('https://qforb.sunrisedvp.systems', 'GET');
        $request->addHeader('Pjsbot76463', '99642');
        $response = $client->getMessageFactory()->createResponse();
        $client->send($request, $response);

        /**
         * @see JonnyW\PhantomJs\Http\CaptureRequest
         **/
        $request = $client->getMessageFactory()->createPdfRequest(URL::site('/reports/quality/saved/'.$report->pk(),'https'), 'GET',15000);
        $request->addHeader('Pjsbot76463', '99642');

        $request->setOutputFile((DOCROOT.'media/data/reports/pdf/'.$report->pk().'.pdf'));
        $request->setFormat('A4');
//        $request->setOrientation('landscape');
        $request->setViewportSize(1920, 1690);
        $request->setPaperSize(1960, 1750);
        /**
         * @see JonnyW\PhantomJs\Http\Response
         **/
        $response = $client->getMessageFactory()->createResponse();
        $request->setDelay(3);
        // Send the request
        $client->send($request, $response);
    }
}
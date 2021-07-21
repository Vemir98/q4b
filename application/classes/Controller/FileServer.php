<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 22.01.2021
 * Time: 23:06
 */

class Controller_FileServer extends HDVP_Controller
{//fileserver
    public function action_callback(){
//        $a = new GAuthenticator();
//        $code = $a->getCode('5MI26UEQW3T5OU7U');
//        $request = Request::factory('https://fs.qforb.net/api/v1/download-image')
//        ->method(Request::POST)
//        ->post(
//            array(
//                'imageUrl' => 'https://miro.medium.com/max/700/1*mk1-6aYaf_Bes1E3Imhc0A.jpeg',
//                'callbackUrl' => 'https://qforb.net/fileserver/callback'
//            ));
//        $response = $request->execute();
//        echo $response->render();
        //$fs = new FileServer();
        //$fs->addImageTask('https://miro.medium.com/max/700/1*mk1-6aYaf_Bes1E3Imhc0A.jpeg','https://qforb.net/fileserver/callback');
        set_time_limit(0);


//        $count = 0;
//        $extraCount = 0;
//        foreach (ORM::factory('PrPlan')->find_all() as $plan){
//            foreach ($plan->files->find_all() as $file){
//                //$file->path = str_replace('https://fs.qforb.net/storage/old/','',$file->path);
//                $file->path = 'https://fs.qforb.net/storage/old/' . $file->path;
//                $file->remote = 1;
//                $file->save();
//                $count++;
//            }
//
//            foreach ($plan->extra_files->find_all() as $file){
//                //$file->path = str_replace('https://fs.qforb.net/storage/old/','',$file->path);
//                $file->path = 'https://fs.qforb.net/storage/old/' . $file->path;
//                $file->remote = 1;
//                $file->save();
//                $extraCount++;
//            }
//
//        }
//        echo 'Finish, files count - '.$count.'; extra - '.$extraCount;

        $images = ORM::factory('Image')->find_all();
        $count = 0;
            foreach ($images as $i){
                if(strpos($i->path,'et/storage/old/https://fs.qforb.n')){
                    $i->path = str_replace('https://fs.qforb.net/storage/old/https://fs.qforb.net/','https://fs.qforb.net/',$i->path);
                    $i->save();
                    $count++;
                }

            }
        echo 'Finish, files count - '.$count;
    }
    public function action_test(){
//        $pf = ORM::factory('PlanFile',237825);
//        var_dump($pf->alias->as_array());
//        var_dump($pf->getImageLink());
        $fs = new FileServer();
        $fs->addPdfTask(
            'https://qforb.net/media/data/projects/52/plans/5f4ce2503bbd9.pdf',
            'https://qforb.net/fileserver/planaddcallback?planId=1&fileId=2',
            1,
            1
        );
    }

    public function action_test111(){die;
        set_time_limit(0);
//        $pf = ORM::factory('PlanFile',256);
//        echo $pf->path . '/' . $pf->name;
//        $pfa = $pf->alias;
//        $pfa->file_id = 256;
//        $pfa->mobile = $pf->path . '/' . $pf->name;
//        $pfa->save();

        foreach (ORM::factory('PrPlan')->find_all() as $plan){
            foreach ($plan->files->find_all() as $file){
                $path = str_replace('https://fs.qforb.net/storage/old/','',$file->path);

                if(empty($file->name) OR empty($path)){
                    continue;
                }

                //pdf
                if(strpos('.pdf',$file->name)){
                    $filename = explode('.',$file->name);
                    $imageName = $filename[0].'.jpg';
                    $mobileName = $filename[0].'-mobile.jpg';
                }else{//image jpg|png
                    $filename = explode('.',$file->name);
                    $imageName = $filename[0].'.jpg';
                    $mobileName = $filename[0].'-mobile.jpg';
                }

                if( ! file_exists(DOCROOT.'/'.$path.'/'.$imageName)){
                    $imageName = preg_replace('~.jpg$~i','.png',$imageName);
                    if( ! file_exists(DOCROOT.'/'.$path.'/'.$imageName)){
                        $imageName = '';
                    }
                }

                if( ! file_exists(DOCROOT.'/'.$path.'/'.$mobileName)){
                    $mobileName = preg_replace('~.jpg$~i','.png',$mobileName);
                    if( ! file_exists(DOCROOT.'/'.$path.'/'.$mobileName)){
                        $mobileName = '';
                    }
                }

                if(!empty($imageName) OR !empty($mobileName)){
                    $fa = ORM::factory('PlanFileAlias');
                    $fa->file_id = $file->id;
                    if(!empty($mobileName))
                    $fa->mobile = 'https://fs.qforb.net/storage/old/'.$path.'/'.$mobileName;
                    if(!empty($imageName))
                    $fa->image = 'https://fs.qforb.net/storage/old/'.$path.'/'.$imageName;
                    $fa->save();
                }
            }

        }
        echo "DONE!!!";
    }

    public function action_qcimages(){
        set_time_limit(0);
        $qcs = ORM::factory('QualityControl')->find_all();
        $i=0;
        $fs = new FileServer();
        foreach ($qcs as $qc){
            foreach ($qc->images->find_all() as $image){
                if(strpos($image->path,'fs.qforb') === false){
                    $fs->addSimpleImageTask('https://qforb.net/' . $image->path . '/' . $image->name,$image->id);
                }
            }
        }

        echo "DONE!!!" . $i;
    }

    public function action_planImages(){
        set_time_limit(0);
//        $plan = ORM::factory('PrPlan',52893);
//        echo $plan->files->where('remote','=',0)->count_all();die;
        $fs = new FileServer();
        $i=0;
        foreach (ORM::factory('PrPlan')->find_all() as $plan){
            foreach ($plan->files->find_all() as $file) {
                if (empty($file->name) OR empty($file->path) OR $file->remote) {
                    continue;
                }

                //pdf
                if (strtolower($file->ext) == 'pdf' OR strpos('.pdf',$file->name)) {
                    $fs->addLazyPdfTask(
                        'https://qforb.net/' . $file->path . '/' . $file->name,
                        'https://qforb.net/fileserver/planaddcallback?planId=' . $plan->id . '&fileId=' . $file->id,
                        1,
                        1
                    );
                } else {//image jpg|png
                    $fs->addLazySimpleImageTask('https://qforb.net/' . $file->path . '/' . $file->name,$file->id);
                }
                $i++;
            }

        }
        $fs->sendLazyTasks();
        echo "DONE!!! " . $i;
    }

    public function action_planaddcallback(){
//        $post = $_POST;
//        file_put_contents(DOCROOT . 'r.txt',Request::current()->headers('From-Server'));

        $get = Arr::extract($_GET,['planId','fileId']);
        $post = Arr::extract($_POST,['path','pages','mobile']);
        if(empty($get['planId']) OR empty($get['fileId']) OR empty($post['path']) OR empty($post['pages']) OR empty($post['mobile'])){
            throw new HTTP_Exception_404('Page Not Found');
        }

        $plan = ORM::factory('PrPlan',$get['planId']);
        $file = $file = ORM::factory('PlanFile',$get['fileId']);

        $oldFile = DOCROOT. $file->path . '/' . $file->name;
        $path = explode('/',$post['path']);
        $filename = $path[count($path)-1];
        unset($path[count($path)-1]);
        $path = implode('/',$path);
        $file->name = $filename;
        $file->path = $path;
        $file->remote = 1;
        $file->save();

        $fa = ORM::factory('PlanFileAlias');
        $fa->file_id = $file->id;
        $fa->mobile = $post['mobile'][0];
        $fa->image = $post['pages'][0];
        $fa->save();

        @unlink($oldFile);
        unset($post['mobile'][0],$post['pages'][0]);

        if( ! count($post['pages'])) return;
        //make plan with page files

            $i = 2;
            $floors = $plan->floors->find_all();
            $planTmpArr = $plan->as_array();

            unset($planTmpArr['id'],$planTmpArr['updated_by'],$planTmpArr['approved_by']);
//            file_put_contents(DOCROOT . 'planarr.txt',var_export($planTmpArr,true));
            foreach ($post['pages'] as $idx => $p){
                if($i){
                    $newPlan = ORM::factory('PrPlan');
                    $newPlan->values($planTmpArr);
                    $newPlan->_setCreatedBy($plan->created_by);
                    $newPlan->_setUpdatedBy($plan->created_by);
                    $newPlan->scope = Model_PrPlan::getNewScope();
                    $newPlan->name .= ' (copy '.$i.')';
                    $newPlan->save();
                    if(count($floors)){
                        foreach ($floors as $floor){
                            $newPlan->add('floors',$floor->id);
                        }
                    }

                    $newFile = ORM::factory('PlanFile');
                    $tmpArr = $file->as_array();
                    $newFilePath = explode('/',$p);
                    unset($newFilePath[count($newFilePath)-1]);
                    $newFilePath = implode('/',$newFilePath);
                    unset($tmpArr['id']);
                    $newFile->values($tmpArr);
                    $newFile->mime = 'image/jpeg';
                    $newFile->ext = 'jpg';
                    $newFile->name = end(explode('/',$p));
                    $newFile->original_name .= ' (p-'.$i.')';
                    $newFile->path = $newFilePath;
                    $newFile->token = md5($newFile->original_name).base_convert(microtime(false), 10, 36);
                    $newFile->_setCreatedBy($plan->created_by);
                    $newFile->save();
                    $newPlan->add('files', $newFile->pk());

                    $fa = ORM::factory('PlanFileAlias');
                    $fa->file_id = $newFile->pk();
                    $fa->mobile = $post['mobile'][$idx];
                    $fa->image = $post['pages'][$idx];
                    $fa->save();
                }
                $i++;
            }
    }

    public function action_callbackimage(){

            $get = Arr::extract($_GET,['fileId']);
            $post = Arr::extract($_POST,['path']);
            if(empty($get['fileId']) OR empty($post['path'])){
                throw new HTTP_Exception_404('Page Not Found');
            }

            $file = ORM::factory('Image',$get['fileId']);

            $oldFile = DOCROOT. $file->path . '/' . $file->name;
            $path = explode('/',$post['path']);
            $filename = $path[count($path)-1];
            unset($path[count($path)-1]);
            $path = implode('/',$path);
            $file->name = $filename;
            $file->path = $path;
            $file->remote = 1;
            $file->save();

            @unlink($oldFile);

    }

    public function action_callbackplantrackingfile(){

        $get = Arr::extract($_GET,['fileId']);
        $post = Arr::extract($_POST,['path']);
        if(empty($get['fileId']) OR empty($post['path'])){
            throw new HTTP_Exception_404('Page Not Found');
        }

        $tracking = ORM::factory('PlanTracking',$get['fileId']);

        $oldFile = DOCROOT. $tracking->file;
        $tracking->file = $post['path'];
        $tracking->save();

        @unlink($oldFile);

    }
}
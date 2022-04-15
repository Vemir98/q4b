<?php
/**
 * Created by PhpStorm.
 * User: Vemir
 * Date: 28.03.2022
 * Time: 16:49
 */

class Controller_Api_Projects_Certificates extends HDVP_Controller_API
{
    /**
     * Create Project Certificate
     * @post
     * https://qforb.net/api/json/<appToken>/projects/<projectId>/certificate
     */
    public function action_project_certificate_post()
    {
        try {
            $projectId = $this->getUIntParamOrDie($this->request->param('projectId'));
            $certificateRequestData = Arr::extract($_POST,
                [
                    'name',
                    'sampleRequired',
                    'craftId',
                    'status',
                    'chapters',
                    'participants'
                ]);

            $certValid = Validation::factory($certificateRequestData);

            $certValid
                ->rule('name', 'not_empty')
                ->rule('craftId', 'not_empty')
                ->rule('status', 'not_empty')
                ->rule('chapters', 'not_empty')
                ->rule('participants', 'not_empty');

            if (!$certValid->check()) {
                throw API_ValidationException::factory(500, 'missing certificate required field');
            }

            Database::instance()->begin();

            $certificateQueryData = [
                'name' => $certificateRequestData['name'],
                'sample_required' => $certificateRequestData['sampleRequired'] ?: '0',
                'craft_id' => $certificateRequestData['craftId'],
                'project_id' => $projectId,
                'status' => $certificateRequestData['status'],
                'approved_at' => ($certificateRequestData['status'] === Enum_ApprovalStatus::Approved) ? time() : null,
                'approved_by' => ($certificateRequestData['status'] === Enum_ApprovalStatus::Approved) ? Auth::instance()->get_user()->id : null,
                'created_at' => time(),
                'created_by' => Auth::instance()->get_user()->id
            ];

            $createdCertificateId = DB::insert('pr_certifications')
                ->columns(array_keys($certificateQueryData))
                ->values(array_values($certificateQueryData))
                ->execute($this->_db)[0];

            if(!empty($certificateRequestData['participants'])) {

                foreach ($certificateRequestData['participants'] as $participant) {
                    $participantRequestData = Arr::extract($participant,
                        [
                            'name',
                            'position',
                        ]);

                    $participantValid = Validation::factory($participantRequestData);

                    $participantValid
                        ->rule('name', 'not_empty')
                        ->rule('position', 'not_empty');

                    if (!$participantValid->check()) {
                        throw API_ValidationException::factory(500, 'missing certificate participants required field');
                    }

                    $participantQueryData = [
                        'pr_cert_id' => $createdCertificateId,
                        'name' => $participantRequestData['name'],
                        'position' => $participantRequestData['position']
                    ];

                    DB::insert('pr_cert_participants')
                        ->columns(array_keys($participantQueryData))
                        ->values(array_values($participantQueryData))
                        ->execute($this->_db);
                }
            }

            if(!empty($certificateRequestData['chapters'])) {
                $projectChapters = Api_DBChapters::getProjectChapters($projectId);
                $projectChaptersIds = array_column($projectChapters, 'id');
                $chapterImagesPath = DOCROOT.'media/data/projects/'.$projectId.'/chapterImages';
                if(!file_exists($chapterImagesPath)) {
                    mkdir($chapterImagesPath, 0777, true);
                }

                foreach ($certificateRequestData['chapters'] as $chapter) {
                    if(!in_array($chapter['chapterId'], $projectChaptersIds)) {
                        throw API_ValidationException::factory(500, 'project chapter error');
                    }

                    $certificateChapterRequestData = Arr::extract($chapter,
                        [
                            'chapterId',
                            'text',
                            'images'
                        ]);

                    $chapterValid = Validation::factory($certificateChapterRequestData);

                    $chapterValid
                        ->rule('chapterId', 'not_empty')
                        ->rule('text', 'not_empty');

                    if (!$chapterValid->check()) {
                        throw API_ValidationException::factory(500, 'missing certificate chapter required field');
                    }

                    $certificateChapterQueryData = [
                        'pr_cert_id' => $createdCertificateId,
                        'pr_chapter_id' => $certificateChapterRequestData['chapterId'],
                        'text' => $certificateChapterRequestData['text']
                    ];

                    $createdChapterId = DB::insert('pr_certifications_chapters')
                        ->columns(array_keys($certificateChapterQueryData))
                        ->values(array_values($certificateChapterQueryData))
                        ->execute($this->_db)[0];

                    if(!empty($certificateChapterRequestData['images'])) {
                        $certificateChapterImagesQueryData = $this->saveImageAndGetQueryData($certificateChapterRequestData['images'], $chapterImagesPath);

                        $fs = new FileServer();

                        if(!empty($certificateChapterImagesQueryData)) {
                            foreach ($certificateChapterImagesQueryData as $queryImageData) {
                                $queryImageData['created_at'] = time();
                                $queryImageData['created_by'] = Auth::instance()->get_user()->id;

                                $createdFileId = DB::insert('files')
                                    ->columns(array_keys($queryImageData))
                                    ->values(array_values($queryImageData))
                                    ->execute($this->_db)[0];

                                DB::insert('pr_certifications_chapters_images')
                                    ->columns(['cert_chapter_id', 'file_id' ])
                                    ->values([$createdChapterId, $createdFileId])
                                    ->execute($this->_db);

                                $fs->addLazySimpleImageTask('https://qforb.net/' . $queryImageData['path'] . '/' . $queryImageData['name'],$createdFileId);
                            }
                            $fs->sendLazyTasks();
                        }
                    }
                }
            }

            $this->updateCertificate($createdCertificateId);

            Database::instance()->commit();

            $this->_responseData = [
                'status' => 'success',
                'id' => $createdCertificateId,
            ];

        }  catch (Exception $e) {
            Database::instance()->rollback();
            Kohana::$log->add(Log::ERROR, '[ERROR [action_project_certificates_post][test] (Exception)]: ' . $e->getMessage());
            echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r($e->getMessage()); echo "</pre>"; exit;
//            throw API_Exception::factory(500,'Operation Error');
        }
    }

    /**
     * Copy Certificates to Project
     * @post
     * https://qforb.net/api/json/<appToken>/projects/<projectId>/certificates/copy
     */
    public function action_project_certificates_copy_post()
    {
        try {
            $companyId = $this->getUIntParamOrDie($this->request->param('companyId'));
            $projectId = $this->getUIntParamOrDie($this->request->param('projectId'));
            $requestData = Arr::extract($_POST,
                [
                    'certificatesIds'
                ]);

            $requestDataValid = Validation::factory($requestData);

            $requestDataValid
                ->rule('certificatesIds', 'not_empty');

            if (!$requestDataValid->check()) {
                throw API_ValidationException::factory(500, 'missing required field');
            }
            $certificatesIds = $requestData['certificatesIds'];

            $certificates = Api_DBProjectCertificates::getProjectCertificatesByCertificatesIds($certificatesIds);
            $certificates = $this->getCertificatesExpandedData($certificates);
            $companyCrafts = Api_DBCompanies::getCompanyCrafts($companyId, ['id','company_id','name','catalog_number','status','related_id']);
            $companyCraftsNames = array_map('trim',array_column($companyCrafts, 'name'));

            Database::instance()->begin();

            foreach ($certificates as $certificate) {
                $certificateCraft = Api_DBCompanies::getCompanyCraftById($certificate['craftId'])[0];
                if(!$certificateCraft) {
                    throw API_ValidationException::factory(500, 'invalid craftId');
                }
                if(!in_array(trim($certificateCraft['name']), $companyCraftsNames)) {
                    $craftQueryData = [
                        'company_id' => $companyId,
                        'name' => trim($certificateCraft['name']),
                        'catalog_number' => $certificateCraft['catalogNumber'],
                        'status' => Enum_Status::Enabled,
                        'related_id' => $certificateCraft['relatedId']
                    ];

                    $certificateCraftId = DB::insert('cmp_crafts')
                        ->columns(array_keys($craftQueryData))
                        ->values(array_values($craftQueryData))
                        ->execute($this->_db)[0];
                } else {
                    foreach ($companyCrafts as $companyCraft) {
                        if(trim($companyCraft['name']) === trim($certificateCraft['name'])) {
                            $certificateCraftId = $companyCraft['id'];
                        }
                    }
                }

                $certificateQueryData = [
                    'name' => $certificate['name'],
                    'sample_required' => $certificate['sampleRequired'],
                    'craft_id' => $certificateCraftId,
                    'project_id' => $projectId,
                    'status' => Enum_ApprovalStatus::Waiting,
                    'approved_at' => null,
                    'approved_by' => null,
                    'created_at' => time(),
                    'created_by' => Auth::instance()->get_user()->id
                ];

                $createdCertificateId = DB::insert('pr_certifications')
                    ->columns(array_keys($certificateQueryData))
                    ->values(array_values($certificateQueryData))
                    ->execute($this->_db)[0];

                if(!empty($certificate['participants'])) {

                    foreach ($certificate['participants'] as $participant) {
                        $participantQueryData = [
                            'pr_cert_id' => $createdCertificateId,
                            'name' => $participant['name'],
                            'position' => $participant['position']
                        ];

                        DB::insert('pr_cert_participants')
                            ->columns(array_keys($participantQueryData))
                            ->values(array_values($participantQueryData))
                            ->execute($this->_db);
                    }
                }

                if(!empty($certificate['chapters'])) {
                    $projectChapters = Api_DBChapters::getProjectChapters($projectId);
                    $projectChaptersNames = array_map('trim',array_column($projectChapters, 'name'));
                    $chapterImagesPath = DOCROOT.'media/data/projects/'.$projectId.'/chapterImages';
                    if(!file_exists($chapterImagesPath)) {
                        mkdir($chapterImagesPath, 0777, true);
                    }

                    foreach ($certificate['chapters'] as $chapter) {
                        if(!in_array(trim($chapter['name']), $projectChaptersNames)) {
                            $projectChapterQueryData = [
                                'project_id' => $projectId,
                                'name' => trim($chapter['name']),
                            ];

                            $projectChapterId = DB::insert('pr_chapters')
                                ->columns(array_keys($projectChapterQueryData))
                                ->values(array_values($projectChapterQueryData))
                                ->execute($this->_db)[0];
                        } else {
                            foreach ($projectChapters as $projectChapter) {
                                if(trim($projectChapter['name']) === trim($chapter['name'])) {
                                    $projectChapterId = $projectChapter['id'];
                                }
                            }
                        }

                        $certificateChapterQueryData = [
                            'pr_cert_id' => $createdCertificateId,
                            'pr_chapter_id' => $projectChapterId,
                            'text' => trim($chapter['text'])
                        ];

                        $createdCertificateChapterId = DB::insert('pr_certifications_chapters')
                            ->columns(array_keys($certificateChapterQueryData))
                            ->values(array_values($certificateChapterQueryData))
                            ->execute($this->_db)[0];

                        if(!empty($chapter['images'])) {
                            $certificateChapterImagesQueryData = $this->copyImageAndGetQueryData($chapter['images'], $chapterImagesPath);
                            $fs = new FileServer();

                            if(!empty($certificateChapterImagesQueryData)) {

                                foreach ($certificateChapterImagesQueryData as $queryImageData) {
                                    $queryImageData['created_at'] = time();
                                    $queryImageData['created_by'] = Auth::instance()->get_user()->id;

                                    $createdFileId = DB::insert('files')
                                        ->columns(array_keys($queryImageData))
                                        ->values(array_values($queryImageData))
                                        ->execute($this->_db)[0];

                                    DB::insert('pr_certifications_chapters_images')
                                        ->columns(['cert_chapter_id', 'file_id' ])
                                        ->values([$createdCertificateChapterId, $createdFileId])
                                        ->execute($this->_db);

                                    $fs->addLazySimpleImageTask('https://qforb.net/' . $queryImageData['path'] . '/' . $queryImageData['name'],$createdFileId);
                                }
                                $fs->sendLazyTasks();
                            }
                        }
                    }
                }
                $this->updateCertificate($createdCertificateId);
            }

            Database::instance()->commit();

            $this->_responseData = [
                'status' => 'success',
            ];

        }  catch (Exception $e) {
            Database::instance()->rollback();
            Kohana::$log->add(Log::ERROR, '[ERROR [action_project_certificates_copy_post][test] (Exception)]: ' . $e->getMessage());
//            echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r($e->getMessage()); echo "</pre>"; exit;
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    /**
     * Update Project Certificate
     * @put
     * https://qforb.net/api/json/<appToken>/projects/certificate/<certificateId>
     */
    public function action_project_certificate_put()
    {
        try {
            $certificateId = $this->getUIntParamOrDie($this->request->param('certificateId'));

            $certificateRequestData = Arr::extract($this->put(),
                [
                    'name',
                    'sampleRequired',
                    'status',
                    'chapters',
                    'participants'
                ]);

            $certificate = $this->getCertificatesExpandedData(Api_DBProjectCertificates::getProjectCertificatesByCertificatesIds([$certificateId]))[0];

            $certValid = Validation::factory($certificateRequestData);

            $certValid
                ->rule('name', 'not_empty')
                ->rule('status', 'not_empty')
                ->rule('chapters', 'not_empty')
                ->rule('participants', 'not_empty');

            if (!$certValid->check()) {
                throw API_ValidationException::factory(500, 'missing certificate required field');
            }

            Database::instance()->begin();

            $certificateQueryData = [
                'name' => $certificateRequestData['name'],
                'sample_required' => $certificateRequestData['sampleRequired'] ?: '0',
            ];

            if($certificateRequestData['status'] !== $certificate['status']) {
                $certificateQueryData['status'] = $certificateRequestData['status'];

                switch ($certificateRequestData['status']) {
                    case Enum_ApprovalStatus::Waiting:
                        if($this->_user->getRelevantRole('name') !== 'super_admin') {
                            throw API_ValidationException::factory(500, 'can\'t change certificate status');
                        }
                        $certificateQueryData['approved_at'] = null;
                        $certificateQueryData['approved_by'] = null;
                        break;
                    case Enum_ApprovalStatus::Approved:
                        $roles = [
                            'super_admin',
                            'corporate_admin',
                            'corporate_infomanager',
                            'company_admin',
                            'company_infomanager',
                            'company_manager',
                            'general_admin',
                            'general_infomanager',
                            'project_admin'
                        ];
                        if(!in_array($this->_user->getRelevantRole('name'), $roles)) {
                            throw API_ValidationException::factory(500, 'can\'t change certificate status');
                        }
                        $certificateQueryData['approved_at'] = time();
                        $certificateQueryData['approved_by'] = Auth::instance()->get_user()->id;
                }
            }

            DB::update('pr_certifications')
                ->set($certificateQueryData)
                ->where('id', '=', $certificateId)
                ->execute($this->_db);

            if(!empty($certificateRequestData['participants'])) {
                $certificateParticipants = Api_DBProjectCertificates::getProjectCertificatesParticipants([$certificateId]);
                foreach ($certificateParticipants as $certificateParticipant) {
                    if(!in_array($certificateParticipant['id'], array_column($certificateRequestData['participants'], 'id'))) {
                        DB::delete('pr_cert_participants')
                            ->where('id', '=', $certificateParticipant['id'])
                            ->execute($this->_db);
                    }
                }
                foreach ($certificateRequestData['participants'] as $participant) {
                    $participantRequestData = Arr::extract(
                        $participant,
                        ['name','position',]
                    );

                    $participantValid = Validation::factory($participantRequestData);

                    $participantValid
                        ->rule('name', 'not_empty')
                        ->rule('position', 'not_empty');

                    if (!$participantValid->check()) {
                        throw API_ValidationException::factory(500, 'missing certificate participants required field');
                    }

                    if($participant['id']) {
                        $participantQueryData = [
                            'name' => $participantRequestData['name'],
                            'position' => $participantRequestData['position']
                        ];

                        DB::update('pr_cert_participants')
                            ->set($participantQueryData)
                            ->where('id', '=', $participant['id'])
                            ->execute($this->_db);
                    } else {
                        $participantQueryData = [
                            'pr_cert_id' => $certificateId,
                            'name' => $participantRequestData['name'],
                            'position' => $participantRequestData['position']
                        ];

                        DB::insert('pr_cert_participants')
                            ->columns(array_keys($participantQueryData))
                            ->values(array_values($participantQueryData))
                            ->execute($this->_db);
                    }
                }
            }

            if(!empty($certificateRequestData['chapters'])) {
                $projectId = $certificate['projectId'];
                $projectChapters = Api_DBChapters::getProjectChapters($projectId);
                $projectChaptersIds = array_column($projectChapters, 'id');
                $chapterImagesPath = DOCROOT.'media/data/projects/'.$projectId.'/chapterImages';
                if(!file_exists($chapterImagesPath)) {
                    mkdir($chapterImagesPath, 0777, true);
                }
                $certificateChaptersImagesIds = [];
                $chaptersToDelete = [];
                $chaptersImagesToDelete = [];

                foreach ($certificateRequestData['chapters'] as $chapter) {
                    if(!empty($chapter['images'])) {
                        foreach ($chapter['images'] as $chapterImage) {
                            $certificateChaptersImagesIds[] = $chapterImage['id'];
                        }
                    }
                }

                foreach ($certificate['chapters'] as $certificateChapter) {
                    if(!in_array($certificateChapter['id'], array_column($certificateRequestData['chapters'], 'id'))) {
                        $chaptersToDelete[] = $certificateChapter['id'];
                        $chaptersImagesToDelete = array_merge($chaptersImagesToDelete, array_column($certificateChapter['images'], 'id'));
                    }
                    if(!empty($certificateChapter['images'])) {
                        foreach ($certificateChapter['images'] as $image) {
                            if(!in_array($image['id'], $certificateChaptersImagesIds)) {
                                $chaptersImagesToDelete[] = $image['id'];
                            }
                        }
                    }
                }

                if(!empty($chaptersToDelete)) {
                    DB::delete('pr_certifications_chapters')
                        ->where('id','IN', DB::expr("(".implode(',', $chaptersToDelete).")"))
                        ->execute($this->_db);
                }

                if(!empty($chaptersImagesToDelete)) {
                    DB::delete('files')
                        ->where('id','IN', DB::expr("(".implode(',', $chaptersImagesToDelete).")"))
                        ->execute($this->_db);
                }

                foreach ($certificateRequestData['chapters'] as $chapter) {
                    if(!in_array($chapter['chapterId'], $projectChaptersIds)) {
                        throw API_ValidationException::factory(500, 'project chapter error');
                    }

                    $certificateChapterRequestData = Arr::extract($chapter,
                        [
                            'chapterId',
                            'text',
                            'images'
                        ]);

                    $chapterValid = Validation::factory($certificateChapterRequestData);

                    $chapterValid
                        ->rule('chapterId', 'not_empty')
                        ->rule('text', 'not_empty');

                    if (!$chapterValid->check()) {
                        throw API_ValidationException::factory(500, 'missing certificate chapter required field');
                    }

                    $certificateChapterQueryData = [
                        'pr_cert_id' => $certificateId,
                        'pr_chapter_id' => $certificateChapterRequestData['chapterId'],
                        'text' => $certificateChapterRequestData['text']
                    ];

                    if($chapter['id']) {
                        DB::update('pr_certifications_chapters')
                            ->set($certificateChapterQueryData)
                            ->where('id', '=', $chapter['id'])
                            ->execute($this->_db);
                        if(!empty($certificateChapterRequestData['images'])) {
                            $newImages = [];
                            foreach ($certificateChapterRequestData['images'] as $image) {
                                if(!$image['id']) {
                                    $newImages[] = $image;
                                }
                            }
                            $certificateChapterImagesQueryData = $this->saveImageAndGetQueryData($newImages, $chapterImagesPath);

                            $fs = new FileServer();

                            if(!empty($certificateChapterImagesQueryData)) {
                                foreach ($certificateChapterImagesQueryData as $queryImageData) {
                                    $queryImageData['created_at'] = time();
                                    $queryImageData['created_by'] = Auth::instance()->get_user()->id;

                                    $createdFileId = DB::insert('files')
                                        ->columns(array_keys($queryImageData))
                                        ->values(array_values($queryImageData))
                                        ->execute($this->_db)[0];

                                    DB::insert('pr_certifications_chapters_images')
                                        ->columns(['cert_chapter_id', 'file_id' ])
                                        ->values([$chapter['id'], $createdFileId])
                                        ->execute($this->_db);

                                    $fs->addLazySimpleImageTask('https://qforb.net/' . $queryImageData['path'] . '/' . $queryImageData['name'],$createdFileId);
                                }
                                $fs->sendLazyTasks();
                            }
                        }
                    } else {
                        $createdChapterId = DB::insert('pr_certifications_chapters')
                            ->columns(array_keys($certificateChapterQueryData))
                            ->values(array_values($certificateChapterQueryData))
                            ->execute($this->_db)[0];

                        if(!empty($certificateChapterRequestData['images'])) {
                            $certificateChapterImagesQueryData = $this->saveImageAndGetQueryData($certificateChapterRequestData['images'], $chapterImagesPath);

                            $fs = new FileServer();

                            if(!empty($certificateChapterImagesQueryData)) {
                                foreach ($certificateChapterImagesQueryData as $queryImageData) {
                                    $queryImageData['created_at'] = time();
                                    $queryImageData['created_by'] = Auth::instance()->get_user()->id;

                                    $createdFileId = DB::insert('files')
                                        ->columns(array_keys($queryImageData))
                                        ->values(array_values($queryImageData))
                                        ->execute($this->_db)[0];

                                    DB::insert('pr_certifications_chapters_images')
                                        ->columns(['cert_chapter_id', 'file_id' ])
                                        ->values([$createdChapterId, $createdFileId])
                                        ->execute($this->_db);

                                    $fs->addLazySimpleImageTask('https://qforb.net/' . $queryImageData['path'] . '/' . $queryImageData['name'],$createdFileId);
                                }
                                $fs->sendLazyTasks();
                            }
                        }
                    }
                }
            }

            $this->updateCertificate($certificateId);

            Database::instance()->commit();

            $this->_responseData = [
                'status' => 'success',
            ];

        }  catch (Exception $e) {
            Database::instance()->rollback();
            Kohana::$log->add(Log::ERROR, '[ERROR [action_project_certificate_put][test] (Exception)]: ' . $e->getMessage());
//            echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r($e->getMessage()); echo "</pre>"; exit;
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    /**
     * Get Project Certificate By id
     * @get
     * https://qforb.net/api/json/<appToken>/projects/certificate/<certificateId>
     */
    public function action_project_certificate_get()
    {
        try {
            $certificateId = $this->getUIntParamOrDie($this->request->param('certificateId'));

            $certificate = Api_DBProjectCertificates::getProjectCertificatesByCertificatesIds([$certificateId])[0];
            $certificate = $this->getCertificatesExpandedData([$certificate])[0];

            $this->_responseData = [
                'status' => 'success',
                'item' => $certificate,
            ];

        }  catch (Exception $e) {
            Database::instance()->rollback();
            Kohana::$log->add(Log::ERROR, '[ERROR [action_project_certificate_get][test] (Exception)]: ' . $e->getMessage());
            echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r($e->getMessage()); echo "</pre>"; exit;
//            throw API_Exception::factory(500,'Operation Error');
        }
    }

    /**
     * Delete Project Certificate By id
     * @delete
     * https://qforb.net/api/json/<appToken>/projects/certificate/<certificateId>
     */
    public function action_project_certificate_delete()
    {
        try {
            $certificateId = $this->getUIntParamOrDie($this->request->param('certificateId'));

            DB::delete('pr_certifications')->where('id', '=', $certificateId)->execute($this->_db);


            $this->_responseData = [
                'status' => 'success',
            ];

        }  catch (Exception $e) {
//            Database::instance()->rollback();
            Kohana::$log->add(Log::ERROR, '[ERROR [action_project_certificate_delete][test] (Exception)]: ' . $e->getMessage());
            echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r($e->getMessage()); echo "</pre>"; exit;
//            throw API_Exception::factory(500,'Operation Error');
        }
    }

    /**
     * Get Project Certificates
     * @get
     * https://qforb.net/api/json/<appToken>/projects/<projectId>/certificates
     */
    public function action_project_certificates_get()
    {
        try {
            $projectId = $this->getUIntParamOrDie($this->request->param('projectId'));

            $certificates = Api_DBProjectCertificates::getProjectCertificatesByProjectId($projectId);
            $certificates = $this->getCertificatesExpandedData($certificates);

            $this->_responseData = [
                'status' => 'success',
                'items' => $certificates,
            ];

        }  catch (Exception $e) {
            Database::instance()->rollback();
            Kohana::$log->add(Log::ERROR, '[ERROR [action_project_certificates_get][test] (Exception)]: ' . $e->getMessage());
//            echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r($e->getMessage()); echo "</pre>"; exit;
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    /**
     * get Certificate with participants, chapters, chapter images
     * */
    public static function getCertificatesExpandedData($certificates) :array
    {
        $certificatesIds = array_column($certificates, 'id');

        if(!empty($certificatesIds)) {
            $chapters = Api_DBProjectCertificates::getProjectCertificatesChapters($certificatesIds);
            $chaptersIds = array_column($chapters, 'id');
            if(!empty($chaptersIds)) {
                $chapterImages = Api_DBChapters::getProjectChaptersImages($chaptersIds);
            }
            $participants = Api_DBProjectCertificates::getProjectCertificatesParticipants($certificatesIds);

            foreach ($certificates as $certificateKey => $certificate) {
                $certificates[$certificateKey]['chapters'] = [];
                $certificates[$certificateKey]['participants'] = [];

                foreach ($chapters as $chapter) {
                    if($chapter['certificateId'] === $certificate['id']) {
                        $certificates[$certificateKey]['chapters'][] = $chapter;
                        $currentChapterKey = count($certificates[$certificateKey]['chapters']) - 1;
                        $certificates[$certificateKey]['chapters'][$currentChapterKey]['images'] = [];

                        if(isset($chapterImages)) {
                            foreach ($chapterImages as $chapterImage) {
                                if($chapterImage['certChapterId'] === $chapter['id']) {

                                    if($chapterImage['remote']) {
                                        $imageFullPath = $chapterImage['path'].'/'.$chapterImage['name'];
                                    } else {
                                        $imageFullPath = 'https://qforb.sunrisedvp.systems/'.($chapterImage['path'].'/'.$chapterImage['name']);
//                                        $imageFullPath = 'https://qforb.net/'.($chapterImage['path'].'/'.$chapterImage['name']);
                                    }

                                    $chapterImage['fullPath'] = $imageFullPath;
                                    $certificates[$certificateKey]['chapters'][$currentChapterKey]['images'][] = $chapterImage;
                                }
                            }
                        }
                    }
                }

                foreach ($participants as $participant) {
                    if($participant['certificateId'] === $certificate['id']) {
                        $certificates[$certificateKey]['participants'][] = $participant;
                    }
                }
            }
            return $certificates;
        } else {
            return [];
        }
    }

    /**
     * update Certificate (updated_at, updated_by)
     * */
    private function updateCertificate($certificateId) {
        $queryData = [
            'updated_at' => time(),
            'updated_by' => Auth::instance()->get_user()->id
        ];


        DB::update('pr_certifications')
            ->set($queryData)
            ->where('id', '=', $certificateId)
            ->execute($this->_db);
    }


    private function saveImageAndGetQueryData($images, $pathToSave) :array
    {
        $images = $this->_b64Arr($images, $pathToSave);

        $certificateChapterImagesQueryData = [];

        if(!empty($images)) {
            foreach ($images as $image) {
                $certificateChapterImagesQueryData[] = [
                    'name' => $image['name'],
                    'original_name' => $image['tmp_name'],
                    'ext' => $image['type'],
                    'mime' => $image['type'],
                    'path' => str_replace(DOCROOT,'',$pathToSave),
                    'token' => md5($image['name']).base_convert(microtime(false), 10, 36),
                ];
            }
        }
        return $certificateChapterImagesQueryData;
    }

    private function copyImageAndGetQueryData($images, $pathToSave) :array
    {
        $certificateChapterImagesQueryData = [];
        clearstatcache();
        if(!empty($images)) {
            Kohana::$log->add(Log::ERROR, '[$images]: ' . json_encode($images, JSON_PRETTY_PRINT));

            foreach ($images as $image) {
                $imageFullPath = $image['fullPath'];
                Kohana::$log->add(Log::ERROR, '[IMAGE_FULL_PATH]: ' . $imageFullPath);

                if(!file_exists($pathToSave)) {
                    mkdir($pathToSave, 0777, true);
                }

                $curl = curl_init($imageFullPath);
                curl_setopt($curl, CURLOPT_URL, $imageFullPath);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

                $resp = curl_exec($curl);
                curl_close($curl);

                $pathToCopy = $pathToSave.'/'.$image['name'];
                if(file_put_contents($pathToCopy, $resp) !== false) {
                    $certificateChapterImagesQueryData[] = [
                        'name' => $image['name'],
                        'original_name' => $image['originalName'],
                        'ext' => $image['ext'],
                        'mime' => $image['mime'],
                        'path' => str_replace(DOCROOT,'',$pathToSave),
                        'token' => md5($image['name']).base_convert(microtime(false), 10, 36),
                    ];
                };
            }
        }
        return $certificateChapterImagesQueryData;
    }

}
<?php
use JonnyW\PhantomJs\Client;
use Helpers\PushHelper;


class Controller_Api_Projects_Dashboard extends HDVP_Controller_API
{
    /**
     * Generate statistics of projects
     * https://qforb.net/api/json/<appToken>/projects/statistics
     * GET params {projectIds,from,to}
     */
    public function action_statistics_qc_get(){
        $filters = Arr::extract($_GET,
            [
                'projectIds',
                'from',
                'to'
            ]);

        try {
            $valid = Validation::factory($filters);

            $valid
                ->rule('projectIds', 'not_empty')
                ->rule('from', 'not_empty')
                ->rule('to', 'not_empty');

            if (!$valid->check()) {
                throw API_ValidationException::factory(500, 'missing required field');
            }

            $qcStatistics = Api_DBQualityControl::getProjectsQcListCountsByStatus($filters);

            $result = [
                'total' => 0,
                'invalid' => 0,
                'repaired' => 0,
                'others' => 0
            ];

            foreach ($qcStatistics as $item) {
                switch ($item['status']) {
                    case Enum_QualityControlStatus::Invalid:
                        $result[Enum_QualityControlStatus::Invalid] = (int)$item['count'];
                        break;
                    case Enum_QualityControlStatus::Repaired:
                        $result[Enum_QualityControlStatus::Repaired] = (int)$item['count'];
                        break;
                    default:
                        $result['others'] += (int)$item['count'];
                        break;
                }
                $result['total'] += (int)$item['count'];
            }

            $this->_responseData = [
                'status' => "success",
                'item' => $result
            ];

        } catch (API_ValidationException $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Incorrect data');
        } catch (Exception $e){
            Database::instance()->rollback();
//            throw API_Exception::factory(500,'Operation Error');
            echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r([$e->getMessage()]); echo "</pre>"; exit;
        }
    }

    public function action_statistics_place_get() {

        $filters = Arr::extract($_GET,
            [
                'projectIds',
                'from',
                'to'
            ]);

        try {
            $valid = Validation::factory($filters);

            $valid
                ->rule('projectIds', 'not_empty')
                ->rule('from', 'not_empty')
                ->rule('to', 'not_empty');

            if (!$valid->check()) {
                throw API_ValidationException::factory(500, 'missing required field');
            }

            $places = Api_DBPlaces::getProjectsPlacesCountsByType($filters);

            $result = [
                'total' => [
                    'total' => 0,
                    'withQc' => 0,
                    'withoutQc' => 0
                ],
                'public' => [
                    'total' => 0,
                    'withQc' => 0,
                    'withoutQc' => 0
                ],
                'private' => [
                    'total' => 0,
                    'withQc' => 0,
                    'withoutQc' => 0
                ],
            ];

            foreach ($places as $placeGroup) {
                switch ($placeGroup['type']) {
                    case Enum_ProjectPlaceType::PublicS:
                        $result[Enum_ProjectPlaceType::PublicS]['total'] = (int)$placeGroup['count'];
                        break;
                    case Enum_ProjectPlaceType::PrivateS:
                        $result[Enum_ProjectPlaceType::PrivateS]['total'] = (int)$placeGroup['count'];
                        break;
                }
                $result['total']['total'] += (int)$placeGroup['count'];
            }

            $placesWithQc = Api_DBPlaces::getProjectsPlacesCountsWithQcByType($filters);

            foreach ($placesWithQc as $placeGroup) {
                switch ($placeGroup['type']) {
                    case Enum_ProjectPlaceType::PublicS:
                        $result[Enum_ProjectPlaceType::PublicS]['withQc'] = (int)$placeGroup['count'];
                        break;
                    case Enum_ProjectPlaceType::PrivateS:
                        $result[Enum_ProjectPlaceType::PrivateS]['withQc'] = (int)$placeGroup['count'];
                        break;
                }
                $result['total']['withQc'] += (int)$placeGroup['count'];
            }
            $result['total']['withoutQc'] = (int)$result['total']['total'] - (int)$result['total']['withQc'];
            $result[Enum_ProjectPlaceType::PublicS]['withoutQc'] = (int)$result[Enum_ProjectPlaceType::PublicS]['total'] - (int)$result[Enum_ProjectPlaceType::PublicS]['withQc'];
            $result[Enum_ProjectPlaceType::PrivateS]['withoutQc'] = (int)$result[Enum_ProjectPlaceType::PrivateS]['total'] - (int)$result[Enum_ProjectPlaceType::PrivateS]['withQc'];

            $this->_responseData = [
                'status' => "success",
                'item' => $result
            ];

        } catch (API_ValidationException $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Incorrect data');
        } catch (Exception $e){
            Database::instance()->rollback();
//            throw API_Exception::factory(500,'Operation Error');
            echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r([$e->getMessage()]); echo "</pre>"; exit;
        }
    }

    public function action_statistics_ear_get() {

        $filters = Arr::extract($_GET,
            [
                'projectIds',
                'from',
                'to'
            ]);

        try {

            $valid = Validation::factory($filters);

            $valid
                ->rule('projectIds', 'not_empty')
                ->rule('from', 'not_empty')
                ->rule('to', 'not_empty');

            if (!$valid->check()) {
                throw API_ValidationException::factory(500, 'missing required field');
            }

            $earStatistics = Api_DBElApprovals::getProjectsElApprovalsStatistics($filters);

            $result = [
                'total' => 0,
                'appropriate' => 0,
                'notAppropriate' => 0
            ];

            foreach ($earStatistics as $earGroup) {
                switch ((int)$earGroup['appropriate']) {
                    case 0:
                        $result['notAppropriate'] = (int)$earGroup['count'];
                        break;
                    case 1:
                        $result['appropriate'] = (int)$earGroup['count'];
                        break;
                }
                $result['total'] += (int)$earGroup['count'];
            }

            $this->_responseData = [
                'status' => "success",
                'item' => $result
            ];

        } catch (API_ValidationException $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Incorrect data');
        } catch (Exception $e){
            Database::instance()->rollback();
//            throw API_Exception::factory(500,'Operation Error');
            echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r([$e->getMessage()]); echo "</pre>"; exit;
        }

        return $result;
    }

    public function action_statistics_delivery_get() {

        $filters = Arr::extract($_GET,
            [
                'projectIds',
                'from',
                'to'
            ]);

        try {

            $valid = Validation::factory($filters);

            $valid
                ->rule('projectIds', 'not_empty')
                ->rule('from', 'not_empty')
                ->rule('to', 'not_empty');

            if (!$valid->check()) {
                throw API_ValidationException::factory(500, 'missing required field');
            }

            $ProjectsDeliveryModuleTasksCounts = Api_DBProjects::getProjectsTasksCountsWithDeliveryModule($filters['projectIds']);

            $filteredProjects = [];

            foreach ($ProjectsDeliveryModuleTasksCounts as $group) {
                if((int)$group['count'] > 0) array_push($filteredProjects, $group['id']);
            }

            $filters['projectIds'] = $filteredProjects;

            $result = [
                'total' => [
                    'total' => 0,
                    'delivery' => 0,
                    'preDelivery' => 0
                ],
                'public' => [
                    'total' => 0,
                    'delivery' => 0,
                    'preDelivery' => 0
                ],
                'private' => [
                    'total' => 0,
                    'delivery' => 0,
                    'preDelivery' => 0
                ],
            ];

            if(!empty($filters['projectIds'])) {
                $places = Api_DBPlaces::getProjectsPlacesCountsByType($filters);

                foreach ($places as $placeGroup) {
                    switch ($placeGroup['type']) {
                        case Enum_ProjectPlaceType::PublicS:
                            $result[Enum_ProjectPlaceType::PublicS]['total'] = (int)$placeGroup['count'];
                            break;
                        case Enum_ProjectPlaceType::PrivateS:
                            $result[Enum_ProjectPlaceType::PrivateS]['total'] = (int)$placeGroup['count'];
                            break;
                    }
                    $result['total']['total'] += (int)$placeGroup['count'];
                }

                $publicDeliveryPlaces = Api_DBDelivery::getProjectsDeliveryPlacesCountsByType($filters, Enum_ProjectPlaceType::PublicS);

                foreach ($publicDeliveryPlaces as $publicDelGroup) {
                    switch ((int)$publicDelGroup['isPreDelivery']) {
                        case 0:
                            $result[Enum_ProjectPlaceType::PublicS]['delivery'] = (int)$publicDelGroup['count'];
                            $result['total']['delivery'] += (int)$publicDelGroup['count'];
                            break;
                        case 1:
                            $result[Enum_ProjectPlaceType::PublicS]['preDelivery'] = (int)$publicDelGroup['count'];
                            $result['total']['preDelivery'] += (int)$publicDelGroup['count'];
                            break;
                    }
                }

                $privateDeliveryPlaces = Api_DBDelivery::getProjectsDeliveryPlacesCountsByType($filters, Enum_ProjectPlaceType::PrivateS);


                foreach ($privateDeliveryPlaces as $privateDelGroup) {
                    switch ((int)$privateDelGroup['isPreDelivery']) {
                        case 0:
                            $result[Enum_ProjectPlaceType::PrivateS]['delivery'] = (int)$privateDelGroup['count'];
                            $result['total']['delivery'] += (int)$privateDelGroup['count'];
                            break;
                        case 1:
                            $result[Enum_ProjectPlaceType::PrivateS]['preDelivery'] = (int)$privateDelGroup['count'];
                            $result['total']['preDelivery'] += (int)$privateDelGroup['count'];
                            break;
                    }
                }
            }


            $this->_responseData = [
                'status' => "success",
                'item' => $result
            ];

        } catch (API_ValidationException $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Incorrect data');
        } catch (Exception $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Operation Error');
        }
    }

    public function action_statistics_labtests_get() {

        $filters = Arr::extract($_GET,
            [
                'projectIds',
                'from',
                'to'
            ]);

        try {

            $valid = Validation::factory($filters);

            $valid
                ->rule('projectIds', 'not_empty')
                ->rule('from', 'not_empty')
                ->rule('to', 'not_empty');

            if (!$valid->check()) {
                throw API_ValidationException::factory(500, 'missing required field');
            }

            $labControls = Api_DBLabtests::getProjectsLabTestsCountsGroupsByStatus($filters);

            $result = [
                'total' => 0,
                'approved' => 0,
                'notApproved' => 0,
            ];

            foreach ($labControls as $labControlsGroup) {
                switch ($labControlsGroup['status']) {
                    case Enum_LabtestStatus::Approve:
                        $result['approved'] = (int)$labControlsGroup['count'];
                        break;
                    case Enum_LabtestStatus::Waiting:
                        $result['notApproved'] = (int)$labControlsGroup['count'];
                        break;
                }
                $result['total'] += (int)$labControlsGroup['count'];
            }

            $this->_responseData = [
                'status' => "success",
                'item' => $result
            ];

        } catch (API_ValidationException $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Incorrect data');
        } catch (Exception $e){
            Database::instance()->rollback();
//            throw API_Exception::factory(500,'Operation Error');
            echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r([$e->getMessage()]); echo "</pre>"; exit;
        }
    }

    public function action_statistics_certificates_get() {

        $filters = Arr::extract($_GET,
            [
                'projectIds',
                'from',
                'to'
            ]);

        try {
            $valid = Validation::factory($filters);

            $valid
                ->rule('projectIds', 'not_empty')
                ->rule('from', 'not_empty')
                ->rule('to', 'not_empty');

            if (!$valid->check()) {
                throw API_ValidationException::factory(500, 'missing required field');
            }

            $certificates = Api_DBCertificates::getProjectsCertificatesCountsByType($filters);

            $result = [
                'total' => 0,
                'approved' => 0,
                'notApproved' => 0
            ];

            foreach ($certificates as $certificateGroup) {
                switch ($certificateGroup['status']) {
                    case Enum_ApprovalStatus::Approved:
                        $result['approved'] = (int)$certificateGroup['count'];
                    break;
                    case Enum_ApprovalStatus::Waiting:
                        $result['notApproved'] = (int)$certificateGroup['count'];
                    break;
                }
                $result['total'] += (int)$certificateGroup['count'];
            }

            $this->_responseData = [
                'status' => "success",
                'item' => $result
            ];

        } catch (API_ValidationException $e){
            Database::instance()->rollback();
            throw API_Exception::factory(500,'Incorrect data');
        } catch (Exception $e){
            Database::instance()->rollback();
//            throw API_Exception::factory(500,'Operation Error');
            echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r([$e->getMessage()]); echo "</pre>"; exit;
        }
    }
}
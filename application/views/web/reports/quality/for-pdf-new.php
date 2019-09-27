
<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 27.03.2019
 * Time: 12:55
 */
$detector = new Mobile_Detect;
$stats = $report->getStats();
$reportEntity = count($report->getObjects()) ? 'objects' : 'projects';
//Инстанс класа дебилизатор для этой тупорылой верстки
$debilizator = new QReportDebilizator($report,$reportEntity);
class QReportDebilizator {
    private $_report;
    private $_entType;
    private $_entities;
    private $_lastIdx = 0;
    private $_stats;
    private $_defects;
    private $_defectsIdx = [];
    public function __construct(QualityReport $report,$entityType)
    {
        $this->_report = $report;
        $this->_entType = $entityType;
        $this->_entities = $this->_report->getProjectsORObjects($this->_entType);
        $this->_entities = array_values($this->_entities);
        $this->_stats = $this->_report->getStats();
        if($this->_report->hasSpecialityDetails()){
            foreach ($this->_report->getCompanyCrafts() as $craft){
                for($i=0; $i < count($this->_entities); $i++){
                    if($this->_report->specHasResult($this->_stats[$this->_entType][$this->_entities[$i]['id']]['craftDefects'][$craft['id']]['total'])){
                        $arr = $this->_stats[$this->_entType][$this->_entities[$i]['id']]['craftDefects'][$craft['id']];
                    $arr['name'] = $craft['name'];
                    $this->_defects[$this->_entities[$i]['id']][] = $arr;
                    }
                }
            }
        }
    }

    public function getMaxDefects($ent1,$ent2){
        $cnt1 = count($this->_defects[$ent1['id']]);
        $cnt2 = count($this->_defects[$ent2['id']]);
        return $cnt1 > $cnt2 ? $cnt1 : $cnt2;
    }
    public function getEntityNexDefect($entity){
        if(!empty($this->_defects[$entity['id']])){
            if(!isset($this->_defectsIdx[$entity['id']])){
                $this->_defectsIdx[$entity['id']] = 0;
            }else{
                $this->_defectsIdx[$entity['id']]++;
            }

            if(isset($this->_defects[$entity['id']][$this->_defectsIdx[$entity['id']]])){
                return $this->_defects[$entity['id']][$this->_defectsIdx[$entity['id']]];
            }
            return null;
        }
        return null;
    }

    public function getEntityByIndex($idx){
        $idx = (int)$idx;
        $this->_lastIdx = $idx;
        if(isset($this->_entities[$idx])){
            return $this->_entities[$idx];
        }
        return null;
    }

    public function getHalfEntitiesCount(){
        $cnt = count($this->_entities);
        if($cnt){
            $cnt = $cnt / 2;
        }
        return $cnt;
    }

    public function getFirstEntity(){
        $this->_lastIdx = 0;
        return $this->_entities[$this->_lastIdx];
    }

    public function getLastIdx(){
        return $this->_lastIdx;
    }

    public function hasNextEntity(){
        $idx = $this->_lastIdx + 1;
        return isset($this->_entities[$idx]);
    }

    public function getNextEntity(){
        $idx = $this->_lastIdx + 1;
        return $this->_entities[$idx];
    }

    public function getColorForEntity($entity){
        return $this->_stats[$this->_entType][$entity['id']]['color'];
    }
}
?>
<!DOCTYPE html>
<html lang="<?=Language::getCurrent()->iso2?>" class="<?=Language::getCurrent()->direction?>" <?=$detector->isMobile() ? 'data-mobile="true"': '' ?>>
<head>
    <meta charset="UTF-8">
    <title>Q4B</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="tkn" content="<?=Security::token(true)?>">
    <meta name="fps" content="<?=(int)ini_get('upload_max_filesize')?>-<?=(int)ini_get('post_max_size')?>">
    <meta name="base-uri" content="<?=URL::base()?>">
    <meta name="current-uri" content="<?=Request::current()->uri()?>">
    <script src="/media/js/jquery-2.2.4.min.js"></script>
    <script src= "/media/js/zingchart.min.js"></script>

    <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">-->
    <style>
        .rtl{
            direction: rtl;
        }
        .ltr{
            direction: ltr;
        }
        .pdf-big-chart-container {
            font-family: sans-serif;
            font-size: 15px;
            margin: 30px 0;
            display: block;
        }
        .pdf-big-chart-container .big-chart-block {
            display: inline-block;
            box-sizing: border-box;
            width: 1210px;
            /*height: 360px;*/
            border: 1px solid rgba(82, 86, 89, 0.31);
        }
        .pdf-main-table{
            width: 100%;
            text-align: center;
        }
        .pdf-top-block-main{
            display: inline-block;
            border: 1px solid rgba(82, 86, 89, 0.31);
            border-radius: 8px;
            width: 90%;
            /*height: 150px;*/
            margin: 20px 0;
            text-align: left;
            line-height: 150px;
            font-family: sans-serif;
            font-size: 15px;
        }
        .rtl .pdf-top-block-main {
            text-align: right;
        }
        .pdf-logo-container{
            display: inline-block;
            width: 150px;
            /*height: 100px;*/
            padding: 25px 20px;
            vertical-align: middle;
            line-height: 1;
        }
        .pdf-logo{
            width: auto;
            height: auto;
            max-height: 100%;
            max-width: 100%;
        }
        .pdf-top-block-text-container{
            display: inline-block;
            vertical-align: middle;
            line-height: 1;
        }
        .pdf-top-block-text-single{
            margin-bottom: 10px;
        }
        .pdf-top-block-icon-container{
            display: inline-block;
            width: 15px;
            height: 15px;
            vertical-align: top;
            line-height: 15px;
            margin-left: 5px;
        }
        .pdf-top-block-icon{
            width: auto;
            height: auto;
            max-height: 100%;
            max-width: 100%;
        }
        .pdf-top-block-blue-title{
            display: inline-block;
            color:#1ebae5;
            margin-left: 3px;
            vertical-align: top;
        }
        .pdf-top-block-title-info{
            display: inline-block;
            vertical-align: top;
        }
        .pdf-pie-chart-container{
            font-family: sans-serif;
            font-size: 15px;
        }
        .rtl .pdf-single-pie-chart-block1{
            margin-left: 5px;
            margin-right:0;
        }
        .pdf-single-pie-chart-block1{
            position: relative;
            box-sizing: border-box;
            width: 600px;
            display: inline-block;
            text-align: left;
            border: 1px solid rgba(82, 86, 89, 0.31);
            height: 350px;
            vertical-align: top;
            margin-right: 5px;
            margin-left:0;
        }
        .rtl .pdf-single-pie-chart-block2{
            margin-right: 5px;
            margin-left:0;
        }
        .pdf-single-pie-chart-block2{
            position: relative;
            box-sizing: border-box;
            width: 600px;
            display: inline-block;
            text-align: left;
            border: 1px solid rgba(82, 86, 89, 0.31);
            height: 350px;
            vertical-align: top;
            margin-left: 5px;
            margin-right:0;
        }
        .pdf-pie-chart-title{
            background: #f2faff;
            color: #003a63;
            height: 56px;
            line-height: 56px;
            padding:  0 5px;
            border-bottom: 1px solid #d4e1ea;
        }
        .pdf-pie-chart-img-container{
            text-align: center;
            padding: 4px 0;
        }
        .pdf-pie-chart-img{
            width: 98%;
            height: auto;
            display: inline-block;
        }
        .pdf-pie-chart-img img, .pdf-pie-chart-img .pdf-pie-chart-img-main{
            width: auto;
            height: auto;
            max-width: 100%;
            max-height: 100%;
        }
        .big-chart-container{
            font-family: sans-serif;
            font-size: 15px;
            margin: 30px 0;
            display: block
        }
        .big-chart-container td{
            display: inline-block;
            box-sizing: border-box;
            width: 1210px;
            height: 360px;
            border: 1px solid rgba(82, 86, 89, 0.31);

        }
        .pdf-big-chart-img{
            width: 100%;
            height:360px;
        }
        .pdf-big-chart-img img{
            width: auto;
            height: auto;
            max-width: 100%;
            max-height: 100%;
        }
        .pdf-big-tables-container{
            display: block;
            text-align: center;
            font-size: 15px;
            font-family: sans-serif;
            margin-top: 10px;
        }
        .pdf-big-tables-container-td1 {
            display: inline-block;
            box-sizing: border-box;
            width: 600px;
            padding: 0 10px 10px 0;
        }
        .pdf-big-tables-container-td2{
            display: inline-block;
            box-sizing: border-box;
            width: 600px;
            padding: 0 0 10px 10px;
        }
        .rtl .pdf-big-tables-container-td1{
            padding: 0 0 10px 10px;
        }
        .rtl .pdf-big-tables-container-td2{
            padding: 0 10px 10px 0;
        }
        .big-table-main-title-container{
            height: 36px;
            line-height: 36px;
        }
        .main-table-square-purple{
            display: inline-block;
            width: 22px;
            height: 22px;
            background: rgb(98, 53, 139);
            border-radius: 6px;
            vertical-align: middle;
        }
        .main-table-square-green{
            display: inline-block;
            width: 22px;
            height: 22px;
            background: rgb(1, 251, 136);
            border-radius: 6px;
            line-height: 1;
            vertical-align: middle;
        }
        .main-table-square-blue {
            display: inline-block;
            width: 22px;
            height: 22px;
            background: rgb(15, 31, 241);
            border-radius: 6px;
            line-height: 1;
            vertical-align: middle;
        }
        .main-table-square-red {
            display: inline-block;
            width: 22px;
            height: 22px;
            background: rgb(187, 3, 12);
            border-radius: 6px;
            line-height: 1;
            vertical-align: middle;
        }
        .big-table-main-title{
            display: inline-block;
            vertical-align: middle;
            color: #003a63;
            font-size: 17px;
        }
        .pdf-main-table-block{
            border-collapse: collapse;
            width: 100%;
            border: 1px solid black;
            margin-top: 8px;

        }
        .pdf-main-table-title-container{
            height: 48px;
            background: #f2faff;
            color: #003a63;
            font-weight: normal;
        }
        .pdf-main-table-title-big{
            border: 1px solid rgba(82, 86, 89, 0.31);
            width: 67%;
            padding-left: 6px;
            font-weight: 500;
            font-size: 15px;
            text-align: left;
        }
        .rtl .pdf-main-table-title-big {
            text-align: right;
            padding-right: 6px;
        }
        .pdf-main-table-small-titles-block{
            border: 1px solid rgba(82, 86, 89, 0.31);
            width: 33%;
        }
        .pdf-main-table-small-titles-block .pdf-wide-title{
            border-bottom: 1px solid rgba(82, 86, 89, 0.31);
            height: 24px;
            line-height: 24px;
            font-weight: 500;
            font-size: 14px;
        }
        .pdf-main-table-small-titles-block .pdf-small-title-block{
            height: 24px;
            line-height: 24px;
            font-size: 0;
        }
        .pdf-main-table-small-titles-block .pdf-small-title-block .pdf-small-title1{
            display: inline-block;
            width: 49%;
            line-height: 24px;
            height: 24px;
            vertical-align: top;
            font-weight: 500;
            font-size: 14px;
            border-right: 1px solid rgba(82, 86, 89, 0.31);
            box-sizing: border-box;
        }
        .rtl .pdf-main-table-small-titles-block .pdf-small-title-block .pdf-small-title1 {
            border-right: none;
            border-left: 1px solid rgba(82, 86, 89, 0.31);
        }
        .pdf-main-table-small-titles-block .pdf-small-title-block .pdf-small-title2{
            display: inline-block;
            width: 50%;
            line-height: 24px;
            height: 24px;
            vertical-align: top;
            font-weight: 500;
            font-size: 14px;
            box-sizing: border-box;
        }
        .pdf-main-table-block .main-table-tr{
            height: 34px;
            background: #fff;
        }
        .pdf-main-table-block .main-table-tr.main-table-tr-bg,
        .pdf-main-table-block .main-table-tr:nth-child(3),
        .pdf-main-table-block .main-table-tr:nth-child(5){
            background: #f2faff;
        }
        .pdf-main-table-block .pdf-main-table-blue-title{
            border: 1px solid rgba(82, 86, 89, 0.31);
            color: #1ebae5;
            text-align: left;
            padding-left: 6px
        }
        .rtl .pdf-main-table-block .pdf-main-table-blue-title {
            text-align: right;
            padding-right: 6px
        }
        .pdf-main-table-block .main-table-tr-points{
            border: 1px solid rgba(82, 86, 89, 0.31);
            font-size: 0;
        }
        .pdf-main-table-block .main-table-points1{
            display:inline-block;
            width: 49%;
            line-height: 34px;
            height: 34px;
            vertical-align: top;
            color: #708492;
            font-size: 14px;
            border-right: 1px solid rgba(82, 86, 89, 0.31);
            border-left:none;
        }
        .rtl .pdf-main-table-block .main-table-points1{
            border-left: 1px solid rgba(82, 86, 89, 0.31);
            border-right:none;
        }
        .pdf-main-table-block .main-table-points2{
            display:inline-block;
            width: 50%;
            line-height: 34px;
            height: 34px;
            vertical-align: top;
            color: #708492;
            font-size: 14px;
        }
        .pdf-small-tables-container{
            display: block;
            text-align: center;
            font-size: 15px;
            font-family: sans-serif;
            margin-top: 20px;
        }
        .pdf-small-tables-single1{
            display: inline-block;
            box-sizing: border-box;
            /*width: 50%;*/
            width: 600px;
            padding: 0 10px 10px 0;
        }
        .rtl .pdf-small-tables-single1 {
            padding: 0 0 10px 10px;
        }
        .pdf-small-tables-single2{
            display: inline-block;
            box-sizing: border-box;
            /*width: 50%;*/
            width: 600px;
            padding: 0 0 10px 10px;
        }
        .rtl .pdf-small-tables-single2 {
            padding: 0 10px 10px 0;
        }
        .pdf-small-table-main-title{
            text-align: left;
            color: #1ebae5;
        }
        .rtl .pdf-small-table-main-title {
            text-align: right;
        }
        .pdf-small-table{
            border-collapse: collapse;
            width: 100%;
            border: 1px solid black;
            margin-top: 8px;

        }
        .small-table-title-tr{
            height: 48px;
            background: #9bc5e1;
            color: #003a63;
            font-weight: normal;
        }
        .small-table-title-main {
            border: 1px solid rgba(82, 86, 89, 0.31);
            font-weight: 500;
            font-size: 13px;
            text-align: left;
            padding-left: 4px;
            padding-right:unset;
        }
        .rtl .small-table-title-main{
            text-align: right;
            padding-right: 4px;
            padding-left:unset;
        }
        .small-table-title-main:nth-child(1){
            width: 22%
        }
        .small-table-title-main:nth-child(2){
            width: 19%
        }
        .small-table-title-main:nth-child(3){
            width: 19%
        }
        .small-table-title-main:nth-child(4){
            width: 20%
        }
        .small-table-title-main:nth-child(5){
            width: 20%
        }
        .pdf-small-table-subtr{
            height: 35px;
            font-size: 13px;
            background: #fff
        }
        .pdf-small-table-subtr:nth-child(2),
        .pdf-small-table-subtr:nth-child(4){
            background: #f2faff;
        }
        .pdf-small-table-subtr-bg-blue {
            height: 35px;
            font-size: 13px;
            background: #3fd0f8;
        }
        .pdf-small-table-subtitle {
            border: 1px solid rgba(82, 86, 89, 0.31);
            color: #005c87;
            text-align: left;
            padding-left: 6px;
            background: #9bc5e1;
            padding-right:unset;
        }
        .rtl .pdf-small-table-subtitle {
            text-align: right;
            padding-right: 6px;
            padding-left:unset;
        }
        .pdf-small-table-subtitle-total {
            border: 1px solid rgba(82, 86, 89, 0.31);
            color: #005c87;
            text-align: left;
            padding-left: 6px;
            padding-right:unset;
        }
        .rtl .pdf-small-table-subtitle-total {
            text-align: right;
            padding-right: 6px;
            padding-left:unset;
        }
        .pdf-small-table-subtr-td {
            border: 1px solid rgba(82, 86, 89, 0.31);
            color: #1ebae5;
            text-align: left;
            padding-left: 6px;
            padding-right: unset;
        }
        .rtl .pdf-small-table-subtr-td {
            text-align: right;
            padding-right: 6px;
            padding-left:unset;
        }
        .pdf-small-table-subtd-total {
            border: 1px solid rgba(82, 86, 89, 0.31);
            color: #005c87;
            text-align: left;
            padding-left: 6px;
        }
        .rtl .pdf-small-table-subtd-total {
            text-align: right;
            padding-right: 6px;
            padding-left:unset;
        }
        .pdf-break {
            page-break-after: always;
        }
        .pdf-textarea-container{
            display: block;
            text-align: center;
            font-size: 15px;
            font-family: sans-serif;
            margin-top: 20px;
        }
        .pdf-textarea-block1{
            display: inline-block;
            box-sizing: border-box;
            width: 600px;
            padding: 0 10px 10px 0;
        }
        .rtl .pdf-textarea-block1{
            padding: 0 0 10px 10px;
        }
        .pdf-textarea-block2{
            display: inline-block;
            box-sizing: border-box;
            width: 600px;
            padding: 0 0 10px 10px;
        }
        .rtl .pdf-textarea-block2{
            padding: 0 10px 10px 0;
        }
        .pdf-textarea-title{
            text-align: left;
            margin: 10px 0;
        }
        .rtl .pdf-textarea-title{
            text-align: right;
        }
        .pdf-textarea-info-icon{
            display: inline-block;
            background: #005c8a;
            border-radius: 50%;
            padding: 2px 10px;
            font-size: 17px;
            font-weight: 700;
            color: #fff;
            margin-left:5px;
        }
        .rtl .pdf-textarea-info-icon{
            margin-right:5px;
        }
        .pdf-textarea-title-span{
            color:black;
            font-weight: 600;
            text-align: left;
            display: inline-block;
        }
        .rtl .pdf-textarea-title-span{
            text-align: right;
        }
        .pdf-textarea-container textarea{
            width: 99%;
            margin-top: 5px;
            border: 1px solid #005c8a;
            height: 210px;
        }
        .pdf-small-table-title2{
            display: inline-block;
            /*line-height: 1;*/
            vertical-align: middle;
            color: #1ebae5;
            font-size: 15px;
        }
        .small-table-title-container{
            height: 36px;
            line-height: 36px;
            text-align: left;
            margin-left: 5px;
            margin-right:unset;
        }
        .rtl .small-table-title-container{
            text-align: right;
            margin-right: 5px;
            margin-left:unset;
        }
        .piechart-list {
            text-align: left;
            color: #6fc3dc;
            position: absolute;
            bottom: 5px;
            left: 80px;
        }
        .piechart-list li {
            list-style: none;
            font-size: 15px;
            line-height: 18px;
        }
        .piechart-list ul {
            margin-top:5px;
        }
    </style>
</head>
<body>
    <table class="pdf-main-table">
        <tr>
            <td>
                <div data-name="top-block-main" class="pdf-top-block-main">
                    <div class="pdf-logo-container">
                        <img src="/<?=$report->getCompany()['logo']?>" alt="" class="pdf-logo">
                    </div>
                    <div class="pdf-top-block-text-container">
                        <div class="pdf-top-block-text-single">
                            <span class="pdf-top-block-icon-container">
                                <!--<img src="images/companies.svg" alt="" class="pdf-top-block-icon">-->
                                <i class="icon q4bikon-companies pdf-top-block-icon"></i>
                            </span>
                            <span class="pdf-top-block-blue-title"><?=__('Company name')?>:</span>
                            <span class="pdf-top-block-title-info"><?=$report->getCompany()['name']?></span>
                        </div>
                        <div class="pdf-top-block-text-single">
                            <span class="pdf-top-block-icon-container">
                                <!--<img src="images/project.svg" alt="" class="pdf-top-block-icon">-->
                                <i class="icon q4bikon-project pdf-top-block-icon"></i>
                            </span>
                            <span class="pdf-top-block-blue-title"><?=__('Project name')?>:</span>
                            <span class="pdf-top-block-title-info">
                                <?if(!count($report->getObjects())):?>
                                    <?foreach ($report->getProjects() as $project):?>
                                        <?$tmpProj[] = $project['name']?>
                                    <?endforeach;?>
                                    <?=implode(', ',$tmpProj)?>
                                <?else:?>
                                    <?$tmpProj = $report->getProjects()?>
                                    <?=array_shift($tmpProj)['name']?>
                                <?endif;?>
                                <?unset($tmpProj)?>
                            </span>
                        </div>
                        <div class="pdf-top-block-text-single">
                            <span class="pdf-top-block-icon-container">
                                <!--<img src="images/date.svg" alt="" class="pdf-top-block-icon">-->
                                <i class="q4bikon-date pdf-top-block-icon"></i>
                            </span>
                            <span class="pdf-top-block-blue-title"><?=__('Report Range')?>:</span>
                            <span class="pdf-top-block-title-info"><?=$report->getDateFrom(true)?> - <?=$report->getDateTo(true)?></span>
                        </div>
                        <?if(count($report->getObjects())):?>
                            <div style="margin-bottom: 10px;" class="pdf-top-block-text-single">
                                <span class="light-blue pdf-top-block-icon-container">
                                    <i class="q4bikon-date pdf-top-block-icon"></i>
                                </span>
                                <span class="pdf-top-block-blue-title"><?=__('Structures')?>:</span>
                                <span class="pdf-top-block-title-info">
                                    <?foreach ($report->getObjects() as $object):?>
                                        <?$tmpObj[] = $object['name']?>
                                    <?endforeach;?>
                                    <?=implode(', ',$tmpObj)?>
                                    <? unset($tmpObj)?></span>
                            </div>
                        <?endif;?>
                        <?if(false):?>
                            <div class="pdf-top-block-text-single">
                                <span class="pdf-top-block-icon-container light-blue">
                                    <i class="pdf-top-block-icon q4bikon-date"></i>
                                </span>
                                <span class="pdf-top-block-blue-title"><?=__('Structures')?>:</span>
                                <span class="pdf-top-block-title-info">
                                    <?foreach ($report->getObjects() as $object):?>
                                        <?$tmpObj[] = $object['name']?>
                                    <?endforeach;?>
                                    <?=implode(', ',$tmpObj)?>
                                    <? unset($tmpObj)?>
                                </span>
                            </div>
                        <?endif;?>
                    </div>
                </div>
            </td>
        </tr>
        <tr class="pdf-pie-chart-container">
            <td name="single-pie-chart-block" class="pdf-single-pie-chart-block1">
                <div>
                    <div class="pdf-pie-chart-title">
                        <span><?=__('Status statistics')?></span>
                    </div>
                    <div class="ltr pdf-pie-chart-img-container">
                        <div class="pdf-pie-chart-img">
                            <div id="piechart" class="piechart pdf-pie-chart-img-main"></div>

                        </div>
                        <div class="piechart-list">
                            <ul>
                                <li><?=__(Enum_QualityControlStatus::Existing).' + '.__(Enum_QualityControlStatus::Normal)?>: <?=$report->getStats()['total']['percents']['a']?>%</li>
                                <li><?=__(Enum_QualityControlStatus::Existing).' + '.__(Enum_QualityControlStatus::Normal).' + '.__(Enum_QualityControlStatus::Repaired)?>: <?=$report->getStats()['total']['percents']['b']?>%</li>
<!--                                <li>--><?//=__('Fixed')?><!--:--><?//=$report->getStats()['total']['percents']['fixed']?><!--%</li>-->
                            </ul>
                        </div>
                    </div>
                </div>
            </td>
            <td name="single-pie-chart-block" class="pdf-single-pie-chart-block2">
                <div>
                    <div class="pdf-pie-chart-title">
                        <span><?=__('Status statistics (filtered)')?></span>
                    </div>
                    <div class="ltr pdf-pie-chart-img-container">
                        <div class="pdf-pie-chart-img">
                            <div id="piechart2" class="piechart pdf-pie-chart-img-main"></div>
                        </div>
                        <div class="piechart-list">
                            <ul>
                                <li><?=__(Enum_QualityControlStatus::Existing).' + '.__(Enum_QualityControlStatus::Normal)?>: <?=$report->getStats()['filtered']['percents']['a']?>%</li>
                                <li><?=__(Enum_QualityControlStatus::Existing).' + '.__(Enum_QualityControlStatus::Normal).' + '.__(Enum_QualityControlStatus::Repaired)?>: <?=$report->getStats()['filtered']['percents']['b']?>%</li>
<!--                                <li>--><?//=__('Fixed')?><!--:--><?//=$report->getStats()['filtered']['percents']['fixed']?><!--%</li>-->
                            </ul>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
        <tr class="ltr pdf-big-chart-container">
            <td colspan="2" data-name="big-chart-block" class="big-chart-block">
                <div class="pdf-big-chart-img">
                    <div id="barchart" style="box-shadow: 1px 1px 12px 1px #CDD0D7; border: 1px solid #d4e1ea;"></div>
                </div>
            </td>
        </tr>
         <?php
        $entity = $debilizator->getFirstEntity();
        ?>

        <tr data-name="big-tables-container" class="pdf-big-tables-container">
            <td data-name="table-block" class="pdf-big-tables-container-td1">
                <div   data-name="main-table-title" class="big-table-main-title-container">
                    <span style="background:<?=$debilizator->getColorForEntity($entity)?>" class="main-table-square-purple"></span>
                    <span class="big-table-main-title"><?=$entity['name']?></span>
                </div>
                <table class="pdf-main-table-block">
                    <tr class="pdf-main-table-title-container">
                        <th  class="pdf-main-table-title-big"><?=__('Crafts List')?></th>
                        <th  class="pdf-main-table-small-titles-block">
                            <div class="pdf-wide-title"><?=__('Quantity')?></div>
                            <div class="pdf-small-title-block">
                                <div class="pdf-small-title1"><?=__('Total')?></div>
                                <div class="pdf-small-title2"><?=__('Filtered')?></div>
                            </div>
                        </th>
                    </tr>
                    <?$i=1?>
                    <?foreach ($report->getCompanyCrafts() as $craft):?>
                    <?
                    if($i++ > 5) break;
                    $craftStat['total'] = isset($stats[$reportEntity][$entity['id']]['crafts'][$craft['id']]['count']) ? $stats[$reportEntity][$entity['id']]['crafts'][$craft['id']]['count'] : 0;
                    $craftStat['filtered'] = isset($stats[$reportEntity][$entity['id']]['filteredCrafts'][$craft['id']]['count']) ? $stats[$reportEntity][$entity['id']]['crafts'][$craft['id']]['count'] : 0;
                    ?>
                        <tr class="main-table-tr">
                            <td class="pdf-main-table-blue-title"><?=$craft['name']?></td>
                            <td class="main-table-tr-points">
                                <div class="main-table-points1"><?=$craftStat['total']?></div>
                                <div class="main-table-points2"><?=$craftStat['filtered']?></div>
                            </td>
                        </tr>
                    <?endforeach;?>
                </table>
            </td>
            <td data-name="main-table" class="pdf-big-tables-container-td2">
                <?if($debilizator->hasNextEntity()):?>
                <?$entity = $debilizator->getNextEntity()?>
                <div data-name="main-table-title" class="big-table-main-title-container">
                    <span style="background: <?=$debilizator->getColorForEntity($entity)?>;" class="main-table-square-green"></span>
                    <span class="big-table-main-title"><?=$entity['name']?></span>
                </div>
                <table class="pdf-main-table-block">
                    <tr class="pdf-main-table-title-container">
                        <th  class="pdf-main-table-title-big"><?=__('Crafts List')?></th>
                        <th  class="pdf-main-table-small-titles-block">
                            <div class="pdf-wide-title"><?=__('Quantity')?></div>
                            <div class="pdf-small-title-block">
                                <div class="pdf-small-title1"><?=__('Total')?></div>
                                <div class="pdf-small-title2"><?=__('Filtered')?></div>
                            </div>
                        </th>
                    </tr>
                    <?foreach ($report->getCompanyCrafts() as $craft):?>
                        <?
                        if($i++ > 11) break;
                        $craftStat['total'] = isset($stats[$reportEntity][$entity['id']]['crafts'][$craft['id']]['count']) ? $stats[$reportEntity][$entity['id']]['crafts'][$craft['id']]['count'] : 0;
                        $craftStat['filtered'] = isset($stats[$reportEntity][$entity['id']]['filteredCrafts'][$craft['id']]['count']) ? $stats[$reportEntity][$entity['id']]['crafts'][$craft['id']]['count'] : 0;
                        ?>
                    <tr class="main-table-tr">
                        <td class="pdf-main-table-blue-title"><?=$craft['name']?></td>
                        <td class="main-table-tr-points">
                            <div class="main-table-points1"><?=$craftStat['total']?></div>
                            <div class="main-table-points2"><?=$craftStat['filtered']?></div>
                        </td>
                    </tr>
                   <?endforeach;?>
                </table>
                <?endif;?>
            </td>
        </tr>
         <?
        $entity = $debilizator->getFirstEntity();
        ?>
        <tr data-name="small-tables-container" class="pdf-small-tables-container">
            <td data-name="table-block" class="pdf-small-tables-single1">
                <div class="pdf-small-table-main-title"><?=UTF8::ucfirst(__('general'))?></div>
                <table class="pdf-small-table">
                    <tr class="small-table-title-tr">
                        <th class="small-table-title-main"></th>
                        <?foreach (Enum_QualityControlConditionList::toArray() as $item):?>
                            <th class="small-table-title-main"><?=__($item)?></th>
                        <?endforeach;?>

                    </tr>
                     <?$j=0;?>
                     <?foreach (Enum_QualityControlConditionLevel::toArray() as $conditionLevel):?>
                        <?$j++;?>
                        <tr class="pdf-small-table-subtr">
                            <td class="pdf-small-table-subtitle"><?=__($conditionLevel)?></td>
                            <?foreach (Enum_QualityControlConditionList::toArray() as $conditionList):?>
                                <td class="pdf-small-table-subtr-td"><?=$stats[$reportEntity][$entity['id']]['defects'][$conditionLevel][$conditionList]?></td>
                            <?endforeach;?>
                        </tr>
                    <?endforeach;?>

                    <tr class="pdf-small-table-subtr-bg-blue">
                        <td class="pdf-small-table-subtitle-total"><?=__('Total')?></td>
                        <?foreach ($stats[$reportEntity][$entity['id']]['defects']['total'] as $val):?>
                            <td class="pdf-small-table-subtd-total"><?=$val?></td>
                        <?endforeach;?>
                    </tr>
                </table>
            </td>
            <td data-name="table-block" class="pdf-small-tables-single2">
                <?if($debilizator->hasNextEntity()):?>
                <?$entity = $debilizator->getNextEntity()?>
                    <div class="pdf-small-table-main-title"><?=UTF8::ucfirst(__('general'))?></div>
                    <table class="pdf-small-table">
                        <tr class="small-table-title-tr">
                            <th  class="small-table-title-main"></th>
                            <?foreach (Enum_QualityControlConditionList::toArray() as $item):?>
                            <th cclass="small-table-title-main"><?=__($item)?></th>
                        <?endforeach;?>
                        </tr>
                        <?$j=0;?>
                        <?foreach (Enum_QualityControlConditionLevel::toArray() as $conditionLevel):?>
                        <?$j++;?>
                        <tr class="pdf-small-table-subtr" style="<?=($j % 2) ? '' : ' background: #f2faff;'?>">
                            <td class="pdf-small-table-subtitle"><?=__($conditionLevel)?></td>
                            <?foreach (Enum_QualityControlConditionList::toArray() as $conditionList):?>
                                <td class="pdf-small-table-subtr-td"><?=$stats[$reportEntity][$entity['id']]['defects'][$conditionLevel][$conditionList]?></td>
                            <?endforeach;?>
                        </tr>
                        <?endforeach;?>
                        <tr class="pdf-small-table-subtr-bg-blue">

                            <td class="pdf-small-table-subtitle-total"><?=__('Total')?></td>
                            <?foreach ($stats[$reportEntity][$entity['id']]['defects']['total'] as $val):?>
                                <td class="pdf-small-table-subtd-total"><?=$val?></td>
                            <?endforeach;?>
                        </tr>
                    </table>
                <?endif;?>
            </td>
        </tr>
    </table>

    <table data-name="textarea-container" class="pdf-main-table">
        <tr class="pdf-textarea-container">
            <?$entity = $debilizator->getFirstEntity();?>
            <td data-name="textarea-block" class="pdf-textarea-block1">
                <div class="pdf-textarea-title">
                    <span class="pdf-textarea-info-icon">i</span>
                    <span class="pdf-textarea-title-span"><?=__('General opinion')?></span>
                </div>
                <textarea data-id="<?=$entity['id']?>" cols="30" rows="10" ><?=$entity['generalOpinion']?></textarea>
            </td>
            <?$entity = $debilizator->getNextEntity();?>
            <td data-name="textarea-block" class="pdf-textarea-block2">
                <?php if(!empty($entity)):?>
                    <div class="pdf-textarea-title">
                        <span class="pdf-textarea-info-icon">i</span>
                        <span class="pdf-textarea-title-span"><?=__('General opinion')?></span>
                    </div>
                    <textarea cols="30" rows="10" data-id="<?=$entity['id']?>"><?=$entity['generalOpinion']?></textarea>
                <?php endif;?>
            </td>
        </tr>
    </table>
    <?if($report->hasSpecialityDetails()):?>
    <?
        $ent1 = $debilizator->getFirstEntity();
        $ent2 = $debilizator->getNextEntity();
        $maxDefect = $debilizator->getMaxDefects($ent1,$ent2);
    ?>
    <?while ($maxDefect-- > 0):?>
    <table data-name="small-tables-container" class="pdf-main-table">
        <tr data-name="small-tables-container" class="pdf-small-tables-container">

            <td data-name="table-block" class="pdf-small-tables-single1">
                <?$ent1Defect = $debilizator->getEntityNexDefect($ent1);
                if(!empty($ent1Defect)):?>
                    <div class="small-table-title-container">
                        <span style="background: <?=$debilizator->getColorForEntity($ent1)?>;" class="main-table-square-purple"></span>
                        <span class="pdf-small-table-title2"><?=$ent1Defect['name']?></span>
                    </div>
                    <table class="pdf-small-table">
                        <tr class="small-table-title-tr">
                            <th  class="small-table-title-main"></th>
                            <?foreach (Enum_QualityControlConditionList::toArray() as $item):?>
                                <th class="small-table-title-main"><?=__($item)?></th>
                            <?endforeach;?>
                        </tr>
                        <?$j=0;?>
                        <?foreach (Enum_QualityControlConditionLevel::toArray() as $conditionLevel):?>
                            <?$j++;?>
                            <tr class="pdf-small-table-subtr">
                                <td class="pdf-small-table-subtitle"><?=__($conditionLevel)?></td>
                                <?foreach (Enum_QualityControlConditionList::toArray() as $conditionList):?>
                                    <td class="pdf-small-table-subtr-td"><?=$ent1Defect[$conditionLevel][$conditionList]?></td>
                                <?endforeach;?>
                            </tr>
                        <?endforeach;?>

                        <tr class="pdf-small-table-subtr-bg-blue">
                            <td class="pdf-small-table-subtitle-total"><?=__('Total')?></td>
                            <?foreach ($ent1Defect['total'] as $val):?>
                                <td class="pdf-small-table-subtd-total"><?=$val?></td>
                            <?endforeach;?>
                        </tr>
                    </table>
                <?endif;?>
            </td>
            <td data-name="table-block" class="pdf-small-tables-single2">
                <?$ent2Defect = $debilizator->getEntityNexDefect($ent2);
                if(!empty($ent2Defect)):?>
                    <div class="small-table-title-container">
                        <span style="background: <?=$debilizator->getColorForEntity($ent2)?>;" class="main-table-square-green"></span>
                        <span class="pdf-small-table-title2"><?=$ent2Defect['name']?></span>
                    </div>
                    <table class="pdf-small-table">
                        <tr class="small-table-title-tr">
                            <th  class="small-table-title-main"></th>
                            <?foreach (Enum_QualityControlConditionList::toArray() as $item):?>
                                <th class="small-table-title-main"><?=__($item)?></th>
                            <?endforeach;?>
                        </tr>

                        <?$j=0;?>
                        <?foreach (Enum_QualityControlConditionLevel::toArray() as $conditionLevel):?>
                            <?$j++;?>
                            <tr class="pdf-small-table-subtr">
                                <td class="pdf-small-table-subtitle"><?=__($conditionLevel)?></td>
                                <?foreach (Enum_QualityControlConditionList::toArray() as $conditionList):?>
                                    <td class="pdf-small-table-subtr-td"><?=$ent2Defect[$conditionLevel][$conditionList]?></td>
                                <?endforeach;?>
                            </tr>
                        <?endforeach;?>
                        <tr class="pdf-small-table-subtr-bg-blue">
                            <td class="pdf-small-table-subtitle-total"><?=__('Total')?></td>
                            <?foreach ($ent2Defect['total'] as $val):?>
                                <td class="pdf-small-table-subtd-total"><?=$val?></td>
                            <?endforeach;?>
                        </tr>

                    </table>
                <?endif;?>
            </td>
        </tr>
    </table>

    <?endwhile;?>
    <div class="pdf-break"></div>

    <?endif;?>
    <?$iterations = ceil($debilizator->getHalfEntitiesCount()-1);?>
    <?if($iterations):?>
        <?for($i=0;$i<$iterations;$i++):?>
            <?
            $ent1 = $debilizator->getEntityByIndex($i+2);
            $ent2 = $debilizator->getNextEntity();
            ?>
            <table data-name="big-tables-container" class="pdf-main-table">
                <tr class="pdf-big-tables-container">
                    <td data-name="table-block" class="pdf-big-tables-container-td1">
                        <div  data-name="main-table-title" class="big-table-main-title-container">
                            <span style="background: <?=$debilizator->getColorForEntity($ent1)?>;" class="main-table-square-blue"></span>
                            <span class="big-table-main-title"><?=$ent1['name']?></span>
                        </div>
                        <table class="pdf-main-table-block">
                            <tr class="pdf-main-table-title-container">
                                <th  class="pdf-main-table-title-big"><?=__('Crafts List')?></th>
                                <th  class="pdf-main-table-small-titles-block">
                                    <div class="pdf-wide-title"><?=__('Quantity')?></div>
                                    <div class="pdf-small-title-block">
                                        <div class="pdf-small-title1"><?=__('Total')?></div>
                                        <!--<div id="div_7f99_20"></div>-->
                                        <div class="pdf-small-title2"><?=__('Filtered')?></div>
                                    </div>
                                </th>
                            </tr>
                            <?$j=1?>
                            <?foreach ($report->getCompanyCrafts() as $craft):?>
                                <?
                                if($j++ > 5) break;
                                $craftStat['total'] = isset($stats[$reportEntity][$ent1['id']]['crafts'][$craft['id']]['count']) ? $stats[$reportEntity][$ent1['id']]['crafts'][$craft['id']]['count'] : 0;
                                $craftStat['filtered'] = isset($stats[$reportEntity][$ent1['id']]['filteredCrafts'][$craft['id']]['count']) ? $stats[$reportEntity][$ent1['id']]['crafts'][$craft['id']]['count'] : 0;
                                ?>
                                <tr class="main-table-tr">
                                    <td class="pdf-main-table-blue-title"><?=$craft['name']?></td>
                                    <td class="main-table-tr-points">
                                        <div class="main-table-points1"><?=$craftStat['total']?></div>
                                        <div class="main-table-points2"><?=$craftStat['filtered']?></div>
                                    </td>
                                </tr>
                            <?endforeach;?>
                        </table>
                    </td>
                    <td data-name="main-table" class="pdf-big-tables-container-td2">
                        <?if(!empty($ent2)):?>
                            <div data-name="main-table-title" class="big-table-main-title-container">
                                <span style="background: <?=$debilizator->getColorForEntity($ent2)?>;" class="main-table-square-red"></span>
                                <span class="big-table-main-title"><?=$ent2['name']?></span>
                            </div>
                            <table class="pdf-main-table-block">
                                <tr class="pdf-main-table-title-container">
                                    <th  class="pdf-main-table-title-big"><?=__('Crafts List')?></th>
                                    <th  class="pdf-main-table-small-titles-block">
                                        <div class="pdf-wide-title"><?=__('Quantity')?></div>
                                        <div class="pdf-small-title-block">
                                            <div class="pdf-small-title1"><?=__('Total')?></div>
                                            <!--<div id="div_7f99_20"></div>-->
                                            <div class="pdf-small-title2"><?=__('Filtered')?></div>
                                        </div>
                                    </th>
                                </tr>

                                <?$j=1?>
                                <?foreach ($report->getCompanyCrafts() as $craft):?>
                                    <?
                                    if($j++ > 5) break;
                                    $craftStat['total'] = isset($stats[$reportEntity][$ent2['id']]['crafts'][$craft['id']]['count']) ? $stats[$reportEntity][$ent2['id']]['crafts'][$craft['id']]['count'] : 0;
                                    $craftStat['filtered'] = isset($stats[$reportEntity][$ent2['id']]['filteredCrafts'][$craft['id']]['count']) ? $stats[$reportEntity][$ent2['id']]['crafts'][$craft['id']]['count'] : 0;
                                    ?>
                                    <tr class="main-table-tr">
                                        <td class="pdf-main-table-blue-title"><?=$craft['name']?></td>
                                        <td class="main-table-tr-points">
                                            <div class="main-table-points1"><?=$craftStat['total']?></div>
                                            <div class="main-table-points2"><?=$craftStat['filtered']?></div>
                                        </td>
                                    </tr>
                                <?endforeach;?>
                            </table>
                        <?endif;?>
                    </td>

                </tr>
            </table>



            <table data-name="textarea-container" class="pdf-main-table">
                <tr class="pdf-textarea-container">
                    <?$entity = $debilizator->getFirstEntity();?>
                    <td data-name="textarea-block" class="pdf-textarea-block1">
                        <div class="pdf-textarea-title">
                            <span class="pdf-textarea-info-icon">i</span>
                            <span class="pdf-textarea-title-span"><?=__('General opinion')?></span>
                        </div>
                        <textarea data-id="<?=$ent1['id']?>" cols="30" rows="10" ><?=$ent1['generalOpinion']?></textarea>
                    </td>
                    <td data-name="textarea-block" class="pdf-textarea-block2">
                        <?php if(!empty($ent2)):?>
                            <div class="pdf-textarea-title">
                                <span class="pdf-textarea-info-icon">i</span>
                                <span class="pdf-textarea-title-span"><?=__('General opinion')?></span>
                            </div>
                            <textarea cols="30" rows="10" data-id="<?=$ent2['id']?>"><?=$ent2['generalOpinion']?></textarea>
                        <?php endif;?>
                    </td>
                </tr>
            </table>


            <?if($report->hasSpecialityDetails()):?>
                <?
                $maxDefect = $debilizator->getMaxDefects($ent1,$ent2);
                ?>
                <?while ($maxDefect-- > 0):?>
                    <table data-name="small-tables-container" class="pdf-main-table">
                        <tr data-name="small-tables-container" class="pdf-small-tables-container">

                            <td data-name="table-block" class="pdf-small-tables-single1">
                                <?$ent1Defect = $debilizator->getEntityNexDefect($ent1);
                                if(!empty($ent1Defect)):?>
                                    <div class="small-table-title-container">
                                        <span style="background: <?=$debilizator->getColorForEntity($ent1)?>;" class="main-table-square-purple"></span>
                                        <span class="pdf-small-table-title2"><?=$ent1Defect['name']?></span>
                                    </div>
                                    <table class="pdf-small-table">
                                        <tr class="small-table-title-tr">
                                            <th  class="small-table-title-main"></th>
                                            <?foreach (Enum_QualityControlConditionList::toArray() as $item):?>
                                                <th class="small-table-title-main"><?=__($item)?></th>
                                            <?endforeach;?>
                                        </tr>
                                        <?$j=0;?>
                                        <?foreach (Enum_QualityControlConditionLevel::toArray() as $conditionLevel):?>
                                            <?$j++;?>
                                            <tr class="pdf-small-table-subtr">
                                                <td class="pdf-small-table-subtitle"><?=__($conditionLevel)?></td>
                                                <?foreach (Enum_QualityControlConditionList::toArray() as $conditionList):?>
                                                    <td class="pdf-small-table-subtr-td"><?=$ent1Defect[$conditionLevel][$conditionList]?></td>
                                                <?endforeach;?>
                                            </tr>
                                        <?endforeach;?>

                                        <tr class="pdf-small-table-subtr-bg-blue">
                                            <td class="pdf-small-table-subtitle-total"><?=__('Total')?></td>
                                            <?foreach ($ent1Defect['total'] as $val):?>
                                                <td class="pdf-small-table-subtd-total"><?=$val?></td>
                                            <?endforeach;?>
                                        </tr>
                                    </table>
                                <?endif;?>
                            </td>
                            <td data-name="table-block" class="pdf-small-tables-single2">
                                <?$ent2Defect = $debilizator->getEntityNexDefect($ent2);
                                if(!empty($ent2Defect)):?>
                                    <div class="small-table-title-container">
                                        <span style="background: <?=$debilizator->getColorForEntity($ent2)?>;" class="main-table-square-green"></span>
                                        <span class="pdf-small-table-title2"><?=$ent2Defect['name']?></span>
                                    </div>
                                    <table class="pdf-small-table">
                                        <tr class="small-table-title-tr">
                                            <th  class="small-table-title-main"></th>
                                            <?foreach (Enum_QualityControlConditionList::toArray() as $item):?>
                                                <th class="small-table-title-main"><?=__($item)?></th>
                                            <?endforeach;?>
                                        </tr>

                                        <?$j=0;?>
                                        <?foreach (Enum_QualityControlConditionLevel::toArray() as $conditionLevel):?>
                                            <?$j++;?>
                                            <tr class="pdf-small-table-subtr">
                                                <td class="pdf-small-table-subtitle"><?=__($conditionLevel)?></td>
                                                <?foreach (Enum_QualityControlConditionList::toArray() as $conditionList):?>
                                                    <td class="pdf-small-table-subtr-td"><?=$ent2Defect[$conditionLevel][$conditionList]?></td>
                                                <?endforeach;?>
                                            </tr>
                                        <?endforeach;?>
                                        <tr class="pdf-small-table-subtr-bg-blue">
                                            <td class="pdf-small-table-subtitle-total"><?=__('Total')?></td>
                                            <?foreach ($ent2Defect['total'] as $val):?>
                                                <td class="pdf-small-table-subtd-total"><?=$val?></td>
                                            <?endforeach;?>
                                        </tr>

                                    </table>
                                <?endif;?>
                            </td>
                        </tr>
                    </table>

                <?endwhile; ?>

            <?endif;?>
        <?endfor;?>
    <?endif;?>
    </body>
<?=$report->renderPieChartTotal('piechart')?>
<?=$report->renderPieChartFiltered('piechart2')?>
<?=$report->renderBarChart('barchart',$reportEntity)?>
</body>
</html>
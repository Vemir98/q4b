<?defined('SYSPATH') OR die('No direct script access.');?>
<?$range = Arr::extract($_GET,["from","to"]);?>
<?php
//echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r([$tasks]); echo "</pre>"; exit;
?>

<div id='qc-list-printable-new' class="print-reports-list">
    <?foreach ($qcs as $q):?>
    <table class="report-container">
        <thead class="report-header">
        <tr>
            <td class="report-header-cell">
                <div class="header-space">&nbsp;</div>
            </td>
        </tr>
        </thead>

        <tfoot class="report-footer">
        <tr>
            <td class="report-footer-cell">
                <div class="footer-space">&nbsp;</div>
            </td>
        </tr>

        </tfoot>

        <tbody class="report-content">
        <tr>
            <td class="report-content-cell">
                <div>
                    <div class="pdf_main_content">
                        <div class="pdf_main_content_top_new">
                            <div class="column">
                                <ul>
                                    <li class="pdf_main_content_top_list_item">
                                        <span class="pdf_main_content_top_list_item_name first"><?=__('Quality control')?></span>
                                        <span class="pdf_main_content_top_list_item_value first"> #<?=$q->id?></span>
                                    </li>
                                </ul>
                            </div>
                            <div class="column">
                                <ul>
                                    <li class="pdf_main_content_top_list_item">
                                        <span class="pdf_main_content_top_list_item_name"><?=__('Created by')?>:</span>
                                        <span class="pdf_main_content_top_list_item_value "><?=$q->createUser->name?></span>
                                    </li>
                                    <li class="pdf_main_content_top_list_item">
                                        <span class="pdf_main_content_top_list_item_name"><?=__('Due Date')?>:</span>
                                        <span class="pdf_main_content_top_list_item_value"><?=$q->due_date ? date('d/m/Y',$q->due_date): ''?></span>
                                    </li>
                                    <li class="pdf_main_content_top_list_item">
                                        <span class="pdf_main_content_top_list_item_name"><?=__('Responsible profession')?>:</span>
                                        <span class="pdf_main_content_top_list_item_value"><?=$q->profession->name?></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="pdf_main_content_top_new mt-20">
                            <div class="column">
                                <ul>
                                    <li class="pdf_main_content_properties_list_item">
                                        <span class="pdf_main_content_properties_list_item_name"><?=__('Property')?> :</span>
                                        <span class="pdf_main_content_properties_list_item_value"><?=$q->object->name?></span>
                                    </li>
                                    <li class="pdf_main_content_properties_list_item">
                                        <span class="pdf_main_content_properties_list_item_name"><?=__('Floor')?> :</span>
                                        <span class="pdf_main_content_properties_list_item_name">
                                            <?=$q->place->floor->custom_name ? $q->place->floor->custom_name .' &lrm;('. $q->place->floor->number .')&lrm; ' : '<span class="bidi-override">'. $q->place->floor->number .'</span>' ?>
                                        </span>
                                    </li>
                                    <li class="pdf_main_content_properties_list_item">
                                        <span class="pdf_main_content_properties_list_item_name"><?=__('Element')?> :</span>
                                        <span class="pdf_main_content_properties_list_item_value"><?=$q->place->name?></span>
                                    </li>
                                    <li class="pdf_main_content_properties_list_item">
                                        <span class="pdf_main_content_properties_list_item_name"><?=__('Element number')?> :</span>
                                        <span class="pdf_main_content_properties_list_item_value">
                                        <?
                                        $placeNumber = !empty($q->place->custom_number) ? $q->place->custom_number : $q->place->number;
                                        if($q->place->loaded()) echo $placeNumber . ' ' .'<i class="q4bikon-'.$q->place->type.'" style="margin: 0 5px;"></i> ';?>
                                     </span>
                                    </li>

                                    <li class="pdf_main_content_properties_list_item">
                                        <span class="pdf_main_content_properties_list_item_name"><?=__('Space')?> :</span>
                                        <span class="pdf_main_content_properties_list_item_value"><?=$q->space->type->name?></span>
                                    </li>
                                    <li class="pdf_main_content_properties_list_item">
                                        <span class="pdf_main_content_properties_list_item_name"><?=__('Due Date')?>:</span>
                                        <span class="pdf_main_content_properties_list_item_value"><?=$q->due_date ? date('d/m/Y',$q->due_date): ''?></span>
                                    </li>
                                </ul>

                            </div>

                            <div class="column">
                                <ul>
                                    <li class="pdf_main_content_properties_list_item">
                                        <span class="pdf_main_content_properties_list_item_name"><?=__('Stage')?>:</span>
                                        <span class="pdf_main_content_properties_list_item_value"><?=__($q->project_stage)?></span>
                                    </li>
                                    <li class="pdf_main_content_properties_list_item">
                                        <span class="pdf_main_content_properties_list_item_name"><?=__('Crafts')?> :</span>
                                        <span class="pdf_main_content_properties_list_item_value"><?=$q->craft->name?></span>
                                    </li>
                                    <li class="pdf_main_content_properties_list_item">
                                        <span class="pdf_main_content_properties_list_item_name"><?=__('QC status')?>:</span>
                                        <span class="pdf_main_content_properties_list_item_value"><?=__($q->status)?></span>
                                    </li>
                                    <li class="pdf_main_content_properties_list_item">
                                        <span class="pdf_main_content_properties_list_item_name"><?=__('Manager status')?>:</span>
                                        <span class="pdf_main_content_properties_list_item_value"><?=__($q->approval_status)?></span>
                                    </li>
                                    <?if(strlen($q->severity_level)):?>
                                        <li class="pdf_main_content_properties_list_item">
                                            <span class="pdf_main_content_properties_list_item_name"><?=__('Severity Level')?>:</span>
                                            <span class="pdf_main_content_properties_list_item_value"><?=__($q->severity_level)?></span>
                                        </li>
                                    <?endif?>

                                    <?if(strlen($q->condition_list)):?>
                                        <li class="pdf_main_content_properties_list_item">
                                            <span class="pdf_main_content_properties_list_item_name"><?=__('Conditions List')?>:</span>
                                            <span class="pdf_main_content_properties_list_item_value"><?=__($q->condition_list)?></span>
                                        </li>
                                    <?endif?>
                                </ul>
                            </div>
                        </div>

                        <!---Description-->
                        <?if(strlen($q->description)>1):?>
                            <div class="pdf_main_content_description">
                                <div class="pdf_main_content_description_headline fw-700"><?=__('Description')?></div>
                                <?$desc = explode("\n",$q->getDesc(html_entity_decode($q->description), "@##"));
                                foreach ($desc as $line) {?>
                                    <div class="pdf_main_content_description_paragraph"><?=$line?></div>
                                <?}?>
                            </div>
                        <?endif?>

                        <!---Dialog-->
                        <?if(strlen($q->getDialog(html_entity_decode($q->description), "@##", "\n"))>1):?>
                        <div  class="pdf_main_content_corrective_action">
                        <div class="pdf_main_content_corrective_action_headline fw-700"><?=__('Corrective action/Performed work')?></div>
                        <?$desc = explode("\n",$q->getDialog(html_entity_decode($q->description), "@##", "\n"));
                        foreach ($desc as $line) {?>
                        <div class="pdf_main_content_corrective_action_paragraph"><?=$line?></p>
                            <?}?>
                        </div>
                        <?endif?>
                            <?php
                                $itemTasks = $q->tasks->find_all();
                            ?>
                        <!--- Tasks-->
                            <div class="qc-rep-images">
                                <div class="pdf_main_content_description_headline fw-700"><?=__('Tasks')?></div>
                                <div class="qc_tasks_wraper">
                                <?foreach($itemTasks as $task):?>
                                    <div class="qc_task_item selected">
                                        <div class="task_title"><?=__('Task')?> <?=$task->id?></div>
                                        <div class="task_desc_wrap">
                                            <div class="task_descripticon">
                                                <?$desc = explode("\n",$task->name);
                                                foreach ($desc as $line) {?>
                                                    <div><?=html_entity_decode($line)?></div>
                                                <?}?>
                                            </div>
                                            <div class="report_task_status_print selected">
                                                <img src="https://qforb.sunrisedvp.systems/media/img/qc_task_done.png" >
                                            </div>
                                        </div>
                                    </div>

                                <?endforeach?>
                            </div>
                        </div>
                        <!-- end of taskas-->

                        <!--images-->
                        <?$images = $q->images->where('status','=',Enum_FileStatus::Active)->find_all()?>
                        <?if(count($images)):?>
                            <div class="pdf_main_content_images_headline fw-700"><?=__('Attached images')?> <span>(<?=count($images)?>)</span> </div>
                            <div class="qc-rep-images">
                                <?foreach ($images as $number => $img):?>
                                    <?if(($number+2)%2==0):?>
                                        <div class='f0_new'>
                                    <?endif;?>
                                        <div class="qc-rep-img">
                                            <div class="pdf_main_content_image_prop report-plan-item-image">
                                                <span class="pdf_main_content_image_prop1 fw-700"><?=$img->original_name?></span>
                                                <span class="pdf_main_content_image_prop2 fw-700">(<?=__('uploaded')?>: <?=date('d.m.y H:i',$img->created_at)?> )&#x200E;</span>
                                            </div>
                                            <?if ($_SERVER['SERVER_NAME'] === 'qforb.net') :?>
                                                <img src="<?=$img->getBigThumbPath()?>?<?=rand(100000,99999999)?>" alt="<?=$img->original_name?>">
                                            <?else:?>
                                                <img src="<?=$img->originalFilePath() . '?' . uniqid()?>" alt="<?=$img->original_name?>">
                                            <?endif?>
                                        </div>
                                    <?if(($number+2)%2==1 || $number == count($images)):?>
                                        </div>
                                    <?endif;?>
                                <?endforeach;?>
                            </div>
                        <?endif;?>
                    </div>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
    <?endforeach;?>

    <div class="footer fw-700">
        <span>
            <?=__('Copyright © 2017 Q4B').'   '.__('All right reserved')?>
        </span>
    </div>
    <div class="pdf_header-info header">
        <div class="pdf_header_top">
            <div class="pdf_header_top1">
                <span class="report_range_text fw-700">
                    <?=__('Report Range')?>:
                </span>
                <span class="report_range_value"><?=$range['from']?>-<?=$range['to']?></span>
            </div>
            <div class="pdf_header_top2">
                <img class="pdf_logo1" src="/media/img/qforb_logo.png" alt="logo">
                <img class="pdf_logo2" src="/media/img/qforb_iso.png" alt="logo">
            </div>
        </div>
        <div class="pdf_header_body">
            <div class="pdf_header_body_image">
                <?if(!$_PROJECT->image_id):?>
                    <img src="/media/img/camera.png" alt="project images">
                <?else:?>
                    <img src="<?=$_PROJECT->main_image->originalFilePath()?>" alt="project images">
                <?endif?>
            </div>
            <div class="pdf_header_body_paragraphs">
                <div class="pdf_header_body_paragraphs_main">
                    <div class="pdf_main_content_top_new">
                        <div class="column">
                            <ul>
                                <li class="pdf_header_body_paragraphs_main_item">
                                    <span class="pdf_header_body_paragraphs_main_item_name">
                                        <?=__('Company')?>:
                                    </span>
                                    <span class="pdf_header_body_paragraphs_main_item_value">
                                        <?=$_COMPANY->name?>
                                    </span>
                                </li>
                                <li class="pdf_header_body_paragraphs_main_item">
                                    <span class="pdf_header_body_paragraphs_main_item_name">
                                        <?=__('Project')?>:
                                    </span>
                                    <span class="pdf_header_body_paragraphs_main_item_value">
                                        <?=$_PROJECT->name?>
                                    </span>
                                </li>
                                <li class="pdf_header_body_paragraphs_main_item">
                                    <span class="pdf_header_body_paragraphs_main_item_name">
                                        <?=__('Project ID')?>:
                                    </span>
                                    <span class="pdf_header_body_paragraphs_main_item_value">
                                        <?=$_PROJECT->id?>
                                    </span>
                                </li>
                                <li class="pdf_header_body_paragraphs_main_item">
                                    <span class="pdf_header_body_paragraphs_main_item_name">
                                        <?=__('Project Status')?>:
                                    </span>
                                    <span class="pdf_header_body_paragraphs_main_item_value">
                                        <?=__($_PROJECT->status)?>
                                    </span>
                                </li>
                                <li class="pdf_header_body_paragraphs_main_item">
                                    <span class="pdf_header_body_paragraphs_main_item_name">
                                        <?=__('Owner')?>:
                                    </span>
                                    <span class="pdf_header_body_paragraphs_main_item_value">
                                        <?=$_PROJECT->owner?>
                                    </span>
                                </li>
                            </ul>
                        </div>
                        <div class="column nml-100">
                            <ul>
                                <li class="pdf_header_body_paragraphs_main_item">
                                    <span class="pdf_header_body_paragraphs_main_item_name">
                                        <?=__('Structures')?>:
                                    </span>
                                    <span class="pdf_header_body_paragraphs_main_item_value">
                                        <?=$_PROJECT->objects->count_all()?>
                                    </span>
                                </li>
                                <li class="pdf_header_body_paragraphs_main_item">
                                    <span class="pdf_header_body_paragraphs_main_item_name ">
                                         <?=__('Project')?> <?=__('Start Date')?>:
                                    </span>
                                    <span class="pdf_header_body_paragraphs_main_item_value">
                                        <?=date('d/m/Y',$_PROJECT->start_date)?>
                                    </span>
                                </li>
                                <li class="pdf_header_body_paragraphs_main_item">
                                    <span class="pdf_header_body_paragraphs_main_item_name">
                                        <?=__('Project')?> <?=__('End Date')?>:
                                    </span>
                                    <span class="pdf_header_body_paragraphs_main_item_value">
                                        <?=date('d/m/Y',$_PROJECT->end_date)?>
                                    </span>
                                </li>
                                <li class="pdf_header_body_paragraphs_main_item">
                                    <span class="pdf_header_body_paragraphs_main_item_name">
                                        <?=__('Address')?>:
                                    </span>
                                    <span class="pdf_header_body_paragraphs_main_item_value">
                                        <?=$_PROJECT->address?>
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap');

    body{
        font-family: 'Lato', sans-serif;
    }
    .rtl #qc-list-printable-new * {
        text-align: right!important;
    }

    @media print {
        body {-webkit-print-color-adjust: exact;}
        .pdf_main_content{
            -webkit-print-color-adjust: exact;
        }
        .report-header-cell{
            background: #ffff!important;
            -webkit-print-color-adjust: exact;
        }
        #qc-list-printable-new {
            background: rgba(9, 86, 132, 0.05) !important;
        }
        table.report-container {
            page-break-after:always;
            width: 100%;
        }
        .qc-rep-images{
            width: 100%;
            display: block;
            vertical-align: top;
        }
        .qc-rep-img{
            width: 48%;
            display: inline-block;
            vertical-align: top;
            margin-bottom: 15px;
            margin-top: 15px;
        }
        .qc-rep-img:nth-child(even){
            text-align: left;
        }
        .qc-rep-img:nth-child(even) .img-desc{
            margin-right: auto;
        }
        .qc-rep-img img{
            width: 100%;
            height: 260px;
            border: 1px solid #C4C4C4;
            border-radius: 10px;
            object-fit: contain;
            border-radius: 10px;
        }
        .f0_new .qc-rep-img:nth-child(odd) {
            margin-right: 20px !important;
            margin-left: 0 !important;
        }
        .rtl .f0_new .qc-rep-img:nth-child(odd) {
            margin-right: 0 !important;
            margin-left: 20px !important;
        }
        .qc-rep-img  .img-desc{
            width: 90%;
        }
        .qc-rep-img p{
            font-weight: normal;
            font-size: 6px;
            line-height: 7px;
            color: rgba(0, 0, 0, 0.7);
        }
        .qc-rep-img span{
            font-weight: normal;
            font-size: 8px;
            line-height: 9px;
            color: #095684;
        }
    }
    @page  {
        size: 8.27in 11.69in;
        margin: .5in .2in .5in .2in;
        mso-header-margin: .5in;
        mso-footer-margin: .5in;
        mso-paper-source: 0;
    }
    @page
    {
        margin-bottom: 6mm;
    }
    img{
        max-width: 100%;
    }
    ul, li{
        padding: 0;
    }

    .fw-700 {
        font-weight: 700;
    }
    .report-container *{
        box-sizing: border-box;
    }
    .pdf_header_top{
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        width: 100%;
    }
    .report_range_text{
        font-size: 11px;
        font-style: normal;
        line-height: 13px;
        color: rgba(9, 86, 132, 1);
        display: inline-block;
        margin-right: 6px;
        color: #095684 !important;
    }
    .rtl .report_range_text{
        margin-right: 0;
        margin-left: 6px;

    }

    .report_range_value{
        font-size: 11px;
        font-style: normal;
        font-weight: 400;
        line-height: 13px;
        color: rgba(0, 0, 0, 0.7);
    }
    .pdf_header_top2{
        display: flex;
        justify-content: space-between;
        align-items:center;
    }
    .pdf_logo1 {
        width: 100%;
        max-width: 41px;
        margin-right: 20px;
    }
    .pdf_logo2{
        max-width: 50px;
    }
    .pdf_header_body{
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        width: 100%;
        margin-top: 2px;
        background-color: #fff!important;
    }
    .pdf_header_body_image{
        width: 100%;
        max-width: 106px;
        margin-right: 20px;
    }
    .rtl .pdf_header_body_image{
        margin-right: 0;
        margin-left: 20px;
    }

    .pdf_header_body_image img{
        max-height: 100px;
    }
    .pdf_header_body_paragraphs {
        width: 100%;
        flex: 1;
        display: flex;
        justify-content: flex-start;
        flex-direction: column;
        align-items: flex-start;
    }
    .pdf_main_content_top_new {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    ul.pdf_main_content_top_list > li {
        flex: 1;
    }
    .pdf_header_body_paragraphs_main_item{
        display: inline-block;
        width: 100%;
        text-align: left;
        margin: 0;
        padding: 0;
        margin-bottom: 4px;
        max-height: max-content;
    }
    .pdf_header_body_paragraphs_main{
        margin-top: 3px;
    }
    .pdf_header_body_paragraphs_main_item_name{
        font-style: normal;
        font-weight: 700;
        font-size: 11px;
        line-height: 13px;
        color: #095684 !important;
        display: inline-block;
        margin-right: 6px;
    }

    .rtl .pdf_header_body_paragraphs_main_item_name{
        margin-right: 0;
        margin-left: 6px;
    }
    .pdf_header_body_paragraphs_main_item_value{
        font-style: normal;
        font-weight: 400;
        font-size: 11px;
        line-height: 13px;
        color: rgba(0, 0, 0, 0.7);
    }

    .pdf_main_content{
        padding:16px 20px ;
        margin-top: 13px;
        width: 100%;
    }
    .pdf_main_content_top_list_item{
        list-style-type: none;
        width: 100%;
        text-align: left;
        margin: 0;
        padding: 0;
        max-height: 12px;
    }
    .pdf_main_content_top_list_item_name.first,
    .pdf_main_content_top_list_item_value.first{
        font-size: 11px;
        font-style: normal;
        font-weight: 500;
        line-height: 14px;
        color: rgba(0, 0, 0, 0.7);
    }
    .pdf_main_content_top_list_item_name {
        font-size: 8px;
        font-style: normal;
        font-weight: 700;
        line-height: 10px;
        text-align: left;
        color: #095684 !important;
        margin-right:6px;
    }
    .rtl .pdf_main_content_top_list_item_name{
        margin-right:0;
        margin-left: 6px;
    }
    .pdf_main_content_top_list_item_value {
        font-size: 8px;
        font-style: normal;
        font-weight: 400;
        line-height: 10px;
        text-align: left;
        color: rgba(0, 0, 0, 0.7);
    }
    .pdf_main_content_properties_list>ul{
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        line-height: 0;
    }
    .pdf_main_content_properties_list_item{
        display: flex;
        width: 100%;
        text-align: left;
        margin: 0;
        padding: 0;
        margin-bottom: 4px;
    }
    .pdf_main_content_properties_list_item_name{
        font-size: 11px;
        font-style: normal;
        font-weight: 700;
        line-height: 13px;
        letter-spacing: 0em;
        text-align: left;
        color: #095684 !important;
        margin-right: 8px;
        margin-left: 0;
        white-space: nowrap;
    }
    .rtl .pdf_main_content_properties_list_item_name{

        margin-right: 0;
        margin-left: 6px;
    }

    .pdf_main_content_properties_list_item_value{
        font-size: 11px;
        font-style: normal;
        font-weight: 400;
        line-height: 13px;
        letter-spacing: 0em;
        text-align: left;
        color: rgba(0, 0, 0, 0.7);

    }
    .pdf_main_content_description_headline,
    .pdf_main_content_corrective_action_headline{
        font-size: 10px;
        font-style: normal;
        line-height: 14px;
        letter-spacing: 0em;
        color: #494A4B;
    }
    .pdf_main_content_description{
        margin-top: 20px;
        margin-bottom: 20px;
    }
    .pdf_main_content_description_paragraph{
        font-size: 10px;
        font-style: normal;
        font-weight: normal;
        line-height: 14px;
        letter-spacing: 0em;
        color: #494A4B;
        margin-top: 10px;
    }
    .pdf_main_content_corrective_action{
        margin-top: 20px;
    }

    .pdf_main_content_corrective_action_paragraph,
    .pdf_main_content_images_headline{
        font-size: 10px;
        font-style: normal;
        line-height: 14px;
        letter-spacing: 0em;
        color: #494A4B;
        margin-top: 10px;
    }
    .pdf_main_content_image_prop{
        justify-content: flex-start;
        display:flex;
        align-items: center;
        margin-bottom: 10px;
    }
    .pdf_main_content_image_prop2{
        font-size: 7px;
        font-style: normal;
        line-height: 8px;
        letter-spacing: 0em;
        color: rgba(0, 0, 0, 0.7);
    }
    .pdf_main_content_image_prop1 {
        margin-left: 6px;
        font-size: 9px;
        font-style: normal;
        line-height: 11px;
        letter-spacing: 0em;
        color: #095684 !important;
        margin-right: 6px;
        margin-left: 0;
    }
    .pdf_main_content_image img  {
        border-radius: 10px;
    }
    .rtl .pdf_main_content_image_prop1 {
        margin-left: 6px;
        margin-right: 0;
    }
    .footer{
        margin-top: 40px;
        font-size: 10px;
        font-style: normal;
        line-height: 12px;
        letter-spacing: 0em;
        text-align: center!important;
    }
    thead.report-header {
        display:table-header-group;
    }
    tfoot.report-footer {
        display:table-footer-group;
    }
    .footer, .footer-space {
        height: 30px;
    }
    .header, .header-space
    {
        height: 170px;
        padding:0 10px;
    }
    .header {
        position: fixed;
        background-color: #fff!important;
        top: 0;
        right: 0;
        left: 0;

    }
    .footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        width: 100%;
    }
    .footer>span{
        text-align: center!important;
    }
    .pdf_main_content_top_new .column {
        max-width: 295px;
        width: 100%;
        flex: 1;
    }
    #qc-list-printable-new .footer {
        text-align: center!important;
    }

    /* tasks*/
    /*.qc_task_item {*/
    /*width: 30%;*/
    /*float: left;*/
    /*!*height: 114px;*!*/
    /*background: #F2F9FF;*/
    /*border: 1px solid #888;*/
    /*border-radius: 5px;*/
    /*padding: 10px 12px;*/
    /*display: inline-block;*/
    /*margin-right: 20px;*/
    /*border-right: 5px solid #1EBCE8;*/
    /*margin-bottom: 16px;*/
    /*position: relative;*/
    /*overflow: hidden;*/
    /*}*/
    .qc_task_item {
        width: 96%;
        background: #F2F9FF;
        border-radius: 5px;
        padding: 10px 25px 10px 12px;
        display: inline-block;
        margin-left: 15px;
        margin-right: 15px;
        border: 1px solid #888;
        border-right: 5px solid #1EBCE8;
        margin-bottom: 5px;
        position: relative;
        overflow: hidden;
        page-break-after: avoid;
        page-break-inside: avoid;
    }
    html.rtl  .qc_task_item {
        padding: 10px 12px 10px 25px;
        border: 1px solid #888;
        border-left: 5px solid #1EBCE8;
       
    }
    .qc_tasks_wraper{
        padding-top: 10px;
        display: block;
        width: 100%;
    }
    .task_title{
        font-size: 10px;
        line-height: normal;
        color: #666769;
        font-family: 'Lato', sans-serif !important;
    }
    .task_descripticon{
        margin-top: 8px;
        font-size: 10px;
        line-height: normal;
        color: #677390;
        display: inline-block;
        /*width: 85%;*/
        width: 100%;
        font-family: 'Lato', sans-serif !important;
    }
    .report_task_status_print{
        width: 21px;
        height: 21px;
        vertical-align: middle;
        text-align: center;
        overflow: hidden;
        position: absolute;
        right: 5px;
        top: 40%;
        display: none;
    }
    .report_task_status_print.selected {
        display: inline-block;
    }
    html.rtl .report_task_status_print {
        right: auto;
        left: 5px;
    }

</style>


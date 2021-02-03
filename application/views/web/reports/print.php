<?defined('SYSPATH') OR die('No direct script access.');?>
<?$range = Arr::extract($_GET,["from","to"]);?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap');

    body{
        font-family: 'Lato', sans-serif;
    }

    @media print {
        body {-webkit-print-color-adjust: exact;}
    }
    @page  {
        size: 8.27in 11.69in;
        margin: .5in .2in .5in .2in;
        mso-header-margin: .5in;
        mso-footer-margin: .5in;
        mso-paper-source: 0;
    }
    table, img, svg {
        break-inside: avoid;
    }
    @page {
        size: A4 ;
    }
    img{
        max-width: 100%;
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
        font-weight: 700;
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
    @media print {
        .report-header-cell{
            background: #ffff!important;
            -webkit-print-color-adjust: exact;
        }
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
    .pdf_header_body_paragraphs_top{
        font-size: 8px;
        font-style: normal;
        font-weight: 700;
        line-height: 10px;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: flex-start;
    }
    .project_start_date_text,.project_end_text{
        display: inline-block;
        margin-right: 3px;
    }
    ul.pdf_header_body_paragraphs_main_items{
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        line-height: 0;

    }
    .pdf_header_body_paragraphs_main_item{
        display: inline-block;
        width: 100%;
        min-width: 50%;
        max-width: 50%;
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
        background: rgba(9, 86, 132, 0.05);
        border: 2px solid #DFEAF2;
        border-radius: 10px;
        margin-top: 13px;
        width: 100%;
    }

    @media print {
        .pdf_main_content{
            background: rgba(9, 86, 132, 0.05)!important;
            -webkit-print-color-adjust: exact;
        }
    }
    .pdf_main_content_top_list{
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
    }
    .pdf_main_content_top_list_item{
        display: inline-block;
        width: 100%;
        min-width: 50%;
        max-width: 50%;
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
    .pdf_main_content_properties_list{
        margin-top: 20px;
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
        min-width: 50%;
        max-width: 50%;
        text-align: left;
        margin: 0;
        padding: 0;
        margin-bottom: 4px;
        max-height: max-content;
    }
    .pdf_main_content_properties_list_item_name{
        font-size: 11px;
        font-style: normal;
        font-weight: 700;
        line-height: 13px;
        letter-spacing: 0em;
        text-align: left;
        color: #095684 !important;
        margin-right: 6px;
        margin-left: 0;
        display: inline-block;
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
        font-weight: 500;
        line-height: 14px;
        letter-spacing: 0em;
        color: #494A4B;
    }
    .pdf_main_content_description{
        margin-top: 20px;
    }
    .pdf_main_content_description_paragraph{
        font-size: 10px;
        font-style: normal;
        font-weight: 500;
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
        font-weight: 500;
        line-height: 14px;
        letter-spacing: 0em;
        color: #494A4B;
        margin-top: 10px;
    }
    .pdf_main_content_images_wraper{
        display: flex;
        flex-wrap: wrap;
        width: 100%;
        margin-bottom: 40px;
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
        font-weight: 700;
        line-height: 8px;
        letter-spacing: 0em;
        color: rgba(0, 0, 0, 0.7);
    }
    .pdf_main_content_image_prop1 {
        margin-left: 6px;
        font-size: 9px;
        font-style: normal;
        font-weight: 700;
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
    .pdf_main_content_image:nth-of-type(odd){
        margin-right: 12px;
        margin-bottom: 16px;
    }

    .rtl .pdf_main_content_image{
        margin-right: 0;
        margin-left: 12px;
    }

    .footer{
        margin-top: 40px;
        font-size: 10px;
        font-style: normal;
        font-weight: 700;
        line-height: 12px;
        letter-spacing: 0em;
        text-align: center!important;
    }
    table.report-container {
        page-break-after:always;
        margin: auto;
    }
    thead.report-header {
        display:table-header-group;
    }
    tfoot.report-footer {
        display:table-footer-group;
    }
    table.report-container div.article {
        page-break-inside: avoid;
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
    footer>span{
        text-align: center!important;
    }
</style>

<div id='qc-list-printable-new' class="print-reports-list">
    <table class="report-container" >
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
    <?foreach ($qcs as $q):?>
        <tr>
            <td class="report-content-cell">
                <div class="content">
                    <div class="pdf_main_content">
                        <div class="pdf_main_content_top">
                            <ul class="pdf_main_content_top_list">
                                <li class="pdf_main_content_top_list_item">
                                    <span class="pdf_main_content_top_list_item_name first"><?=__('Quality control')?></span>
                                    <span class="pdf_main_content_top_list_item_value first"> #<?=$q->id?></span>
                                </li>
                                <li class="pdf_main_content_top_list_item">
                                    <span class="pdf_main_content_top_list_item_name"><?=__('Created by')?>:</span>
                                    <span class="pdf_main_content_top_list_item_value "><?=$q->createUser->name?></span>
                                </li>
                                <li class="pdf_main_content_top_list_item">
                                    <span class="pdf_main_content_top_list_item_name ">
                                       <?=__('Approvement Status')?>:
                                    </span>
                                    <span class="pdf_main_content_top_list_item_value "><?=__($q->approval_status)?> </span>
                                </li>
                                <li class="pdf_main_content_top_list_item">
                                    <span class="pdf_main_content_top_list_item_name "><?=__('Due Date')?>:</span>
                                    <span class="pdf_main_content_top_list_item_value "><?=$q->due_date ? date('d/m/Y',$q->due_date): ''?></span>
                                </li>
                                <li class="pdf_main_content_top_list_item">
                                    <span class="pdf_main_content_top_list_item_name "><?=__('Responsible profession')?>:</span>
                                    <span class="pdf_main_content_top_list_item_value "><?=$q->profession->name?></span>
                                </li>

                            </ul>
                        </div>
                        <div class="pdf_main_content_properties_list">
                            <ul>
                                <li class="pdf_main_content_properties_list_item">
                                    <span class="pdf_main_content_properties_list_item_name"><?=__('Property')?> :</span>
                                    <span class="pdf_main_content_properties_list_item_value"><?=__($q->object->type->name)?> - <?=$q->object->name?></span>
                                </li>
                                <li class="pdf_main_content_properties_list_item">
                                    <span class="pdf_main_content_properties_list_item_name"><?=__('Stage')?>:</span>
                                    <span class="pdf_main_content_properties_list_item_value"><?=__($q->project_stage)?></span>
                                </li>
                                <li class="pdf_main_content_properties_list_item">
                                    <span class="pdf_main_content_properties_list_item_name"><?=__('Floor')?> :</span>
                                    <span class="pdf_main_content_properties_list_item_value"><?=$q->floor->number?></span>
                                </li>
                                <li class="pdf_main_content_properties_list_item">
                                    <span class="pdf_main_content_properties_list_item_name"><?=__('Crafts')?> :</span>
                                    <span class="pdf_main_content_properties_list_item_value"><?=$q->craft->name?></span>
                                </li>
                                <li class="pdf_main_content_properties_list_item">
                                    <span class="pdf_main_content_properties_list_item_name"><?=__('Element')?> :</span>
                                    <span class="pdf_main_content_properties_list_item_value"><?=$q->place->name?></span>
                                </li>


                                <li class="pdf_main_content_properties_list_item">
                                    <span class="pdf_main_content_properties_list_item_name"><?=__('Space')?> :</span>
                                    <span class="pdf_main_content_properties_list_item_value"><?=$q->space->desc ? $q->space->desc : 'Space 1'?></span>
                                </li>
                                <li class="pdf_main_content_properties_list_item">
                                    <span class="pdf_main_content_properties_list_item_name"><?=__('Element number')?> :</span>
                                    <span class="pdf_main_content_properties_list_item_value">
                                        <?
                                        $placeNumber = !empty($q->place->custom_number) ? '('.$q->place->custom_number.')&lrm;' : '';
                                        if($q->place->loaded()) echo $q->place->type. ' ' .$q->place->number.' '.$placeNumber;?>
                                    </span>
                                </li>

                                <li class="pdf_main_content_properties_list_item">
                                    <span class="pdf_main_content_properties_list_item_name"><?=__('Status')?>:</span>
                                    <span class="pdf_main_content_properties_list_item_value"><?=__($q->status)?></span>
                                </li>
                            </ul>
                        </div>

                        <!---Description-->
                        <?if(strlen($q->description)>1):?>
                            <div class="pdf_main_content_description">
                                <div class="pdf_main_content_description_headline"><?=__('Description')?></div>
                                <?$desc = explode("\n",$q->getDesc(html_entity_decode($q->description), "@##"));
                                foreach ($desc as $line) {?>
                                    <div class="pdf_main_content_description_paragraph"><?=$line?></div>
                                <?}?>
                            </div>
                        <?endif?>

                        <!---Dialog-->
                        <?if(strlen($q->getDialog(html_entity_decode($q->description), "@##", "\n"))>1):?>
                            <div  class="pdf_main_content_corrective_action">
                                <div class="pdf_main_content_corrective_action_headline"><?=__('Corrective action/Performed work')?></div>
                                <?$desc = explode("\n",$q->getDialog(html_entity_decode($q->description), "@##", "\n"));
                                foreach ($desc as $line) {?>
                                <div class="pdf_main_content_corrective_action_paragraph"><?=$line?></p>
                                <?}?>
                            </div>
                        <?endif?>

                        <!--images-->
                        <?$images = $q->images->where('status','=',Enum_FileStatus::Active)->find_all()?>
                        <?if(count($images)):?>
                            <div class="pdf_main_content_images">
                                <div class="pdf_main_content_images_headline">Attached images <span>(<?=count($images)?>)</span> </div>
                                <div class="pdf_main_content_images_wraper">
                                    <?foreach ($images as $number => $img):?>
                                        <?if(($number+2)%2==0):?>
                                            <div class='f0'>
                                        <?endif;?>
                                        <div class="pdf_main_content_image print-col-50">
                                            <div class="pdf_main_content_image_prop report-plan-item-image">
                                                <span class="pdf_main_content_image_prop1"><?=$img->original_name?></span>
                                                <span class="pdf_main_content_image_prop2">(<?=__('uploaded')?>: <?=date('d.m.y H:i',$img->created_at)?> )&#x200E;</span>
                                            </div>
                                            <img src="<?=$img->originalFilePath()?>" alt="<?=$img->original_name?>">
                                        </div>
                                        <?if(($number+2)%2==1 || $number == count($images)):?>
                                            </div>
                                        <?endif;?>
                                    <?endforeach?>
                                </div>
                            </div>
                        <?endif;?>
                    </div>
                </div>
            </td>
        </tr>
    <?endforeach;?>

        </tbody>
    </table>
    <div class="footer">
        <span>
            <?=__('Copyright © 2017 Q4B').'   '.__('All right reserved')?>
        </span>
    </div>
    <div class="pdf_header-info header">
        <div class="pdf_header_top">
            <div class="pdf_header_top1">
                <span class="report_range_text">
                    <?=__('Report Range')?>:
                </span>
                <span class="report_range_value"><?=$range['from']?>-<?=$range['to']?></span>
            </div>
            <div class="pdf_header_top2">
                <img class="pdf_logo1" src="/media/img/logo_50X50.png" alt="logo">
                <img class="pdf_logo2" src="/media/img/iso_50X50.png" alt="logo">
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
                <div class="pdf_header_body_paragraphs_top">
                    <div class="project_start">
                        <span class="project_start_date_text ">
                             <?=__('Project')?> <?=__('Start Date')?>:
                        </span>
                        <span class="project_start_date_value">
                            <?=date('d/m/Y',$_PROJECT->start_date)?>
                        </span>
                    </div>
                    <div class="project_end">
                        <span class="project_end_text ">
                            <?=__('Project')?> <?=__('End Date')?>:
                        </span>
                        <span class="project_end_value">
                            <?=date('d/m/Y',$_PROJECT->end_date)?>
                        </span>
                    </div>
                </div>
                <div class="pdf_header_body_paragraphs_main">
                    <ul class="pdf_header_body_paragraphs_main_items">
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
                                <?=__('Owner')?>:
                            </span>
                            <span class="pdf_header_body_paragraphs_main_item_value">
                                <?=$_PROJECT->owner?>
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
                                <?=__('Structures')?>:
                            </span>
                            <span class="pdf_header_body_paragraphs_main_item_value">
                                <?=$_PROJECT->objects->count_all()?>
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
                                <?=__('Address')?>:
                            </span>
                            <span class="pdf_header_body_paragraphs_main_item_value">
                                <?=$_PROJECT->address?>
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
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>


<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 12.05.2020
 * Time: 6:33
 */

$ii = 0;
$floorNumber = $report->floor->number;
if($floorNumber[0] == '-'){
    $floorNumber = substr($floorNumber,1).'-';
}
?>
<!DOCTYPE html>
<html lang="en" class="rtl">
<head>
    <meta charset="UTF-8">
    <title>Personal details</title>
</head>
<body class="rtl">
<link rel="stylesheet" href="/media/css/delivery-reports.css">
<div class="pdf-main">
    <div class="pdf-padding">
        <div class="pdf-heading">
            <div class="pdf-logos">
                <?if($report->quality):?>
                    <img src="/media/img/new-images/quality.png" alt="" class="quality-mark">
                <?endif?>
                <img src="/media/img/new-images/q4b-logo.png" alt="" class="q4b-logo">
            </div>
            <div class="pdf-info">
                <div class="project-logo">
                    <img src="<?=$report->project->main_image->originalFilePath()?>" alt="<?=$project->name?>">
                </div>

                <div class="pdf-lists">
                    <div class="pdf-list-top">
                        <ul class="pdf-ul">
                            <li>פרוטוקול מסירה מס :<span><?=$report->id?></span></li>
                            <li>שם עובד :<span><?=$report->user->name?></span></li>
                        </ul>



                        <ul class="pdf-ul">
                            <li>תאריך :<span><?=date('d/m/Y',$report->created_at)?></span></li>
                        </ul>

                    </div>
                    <div class="pdf-list-bottom">

                        <ul class="pdf-ul">
                            <li>שם הפרויקט :<span><?=$report->project->name?><span></li>
                            <li>מבנה :<span><?=html_entity_decode($report->object->name)?></span></li>
                            <li>קומה :<span><?=$floorNumber?></span></li>
                        </ul>
                        <ul class="pdf-ul">
                            <li>מרחב :<span><?=html_entity_decode(str_replace("'"," ",$report->place->name.' ('.$report->place->custom_number.')'))?></span></li>
                            <li>מחסן/'ם :<span><?=$report->larder?></span></li>
                            <li>חניה/ות :<span><?=$report->parking?></span></li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
        <div class="pdf-content">

            <!--personal details -->
            <div class="pdf-content-title">
                פרטי לקוח/ות
            </div>
            <?foreach ($report->customers->find_all() as $customer):?>
                <div class="pr-dt">
                    <div class="pr-dt-info">
                        <ul class="pdf-ul">
                            <li>שם לקוח :<span><?=$customer->full_name?></span></li>
                            <li>מס. תעודת הזהות :<span><?=$customer->id_number?></span></li>
                            <li>מס. טלפון :<span><?=$customer->phone_number?></span></li>
                            <li> כתובת דוא"ל :<span><?=$customer->email?></span></li>
                        </ul>
                    </div>
                    <div class="img">
                        <img src="/media/data/customers/<?=$customer->file?>" alt="">
                    </div>
                </div>
            <?endforeach;?>
            <!--personal details end-->
        </div>
        <div class="pdf-footer">
            <p>-<?=++$ii?>-</p>
        </div>
    </div>
</div>
<?if($report->poaFiles->count_all()):?>
    <div class="pdf-main">
        <div class="pdf-padding">
            <div class="pdf-heading">
                <div class="pdf-logos">
                    <?if($report->quality):?>
                        <img src="/media/img/new-images/quality.png" alt="" class="quality-mark">
                    <?endif?>
                    <img src="/media/img/new-images/q4b-logo.png" alt="" class="q4b-logo">
                </div>
                <div class="pdf-info">
                    <div class="project-logo">
                        <img src="<?=$report->project->main_image->originalFilePath()?>" alt="<?=$project->name?>">
                    </div>

                    <div class="pdf-lists">
                        <div class="pdf-list-top">
                            <ul class="pdf-ul">
                                <li>פרוטוקול מסירה מס :<span><?=$report->id?></span></li>
                                <li>שם עובד :<span><?=$report->user->name?></span></li>
                            </ul>



                            <ul class="pdf-ul">
                                <li>תאריך :<span><?=date('d/m/Y',$report->created_at)?></span></li>
                            </ul>

                        </div>
                        <div class="pdf-list-bottom">

                            <ul class="pdf-ul">
                                <li>שם הפרויקט :<span><?=$report->project->name?><span></li>
                                <li>מבנה :<span><?=html_entity_decode($report->object->name)?></span></li>
                                <li>קומה :<span><?=$floorNumber?></span></li>
                            </ul>
                            <ul class="pdf-ul">
                                <li>מרחב :<span><?=html_entity_decode(str_replace("'"," ",$report->place->name.' ('.$report->place->custom_number.')'))?></span></li>
                                <li>מחסן/'ם :<span><?=$report->larder?></span></li>
                                <li>חניה/ות :<span><?=$report->parking?></span></li>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
            <div class="pdf-content">
                <!--power attorney-->
                <div class="pdf-content-title">
                    ייפוי כוח
                </div>
                <?foreach ($report->poaFiles->find_all() as $file):?>
                    <div class="pow-att">
                        <p class="pow-title">צילום אישור ייפוי כוח</p>
                        <div class="img">
                            <img src="/<?=$file->path?>/<?=$file->name?>" alt="">
                        </div>
                    </div>
                <?endforeach;?>
                <!--power attorney end-->
            </div>
            <div class="pdf-footer">
                <p>-<?=++$ii?>-</p>
            </div>
        </div>
    </div>
<?endif;?>
<?if(strlen($report->el_pic) OR strlen($report->wm_pic)):?>
<div class="pdf-main">
    <div class="pdf-padding">
        <div class="pdf-heading">
            <div class="pdf-logos">
                <?if($report->quality):?>
                    <img src="/media/img/new-images/quality.png" alt="" class="quality-mark">
                <?endif?>
                <img src="/media/img/new-images/q4b-logo.png" alt="" class="q4b-logo">
            </div>
            <div class="pdf-info">
                <div class="project-logo">
                    <img src="<?=$report->project->main_image->originalFilePath()?>" alt="<?=$project->name?>">
                </div>

                <div class="pdf-lists">
                    <div class="pdf-list-top">
                        <ul class="pdf-ul">
                            <li>פרוטוקול מסירה מס :<span><?=$report->id?></span></li>
                            <li>שם עובד :<span><?=$report->user->name?></span></li>
                        </ul>



                        <ul class="pdf-ul">
                            <li>תאריך :<span><?=date('d/m/Y',$report->created_at)?></span></li>
                        </ul>

                    </div>
                    <div class="pdf-list-bottom">

                        <ul class="pdf-ul">
                            <li>שם הפרויקט :<span><?=$report->project->name?><span></li>
                            <li>מבנה :<span><?=html_entity_decode($report->object->name)?></span></li>
                            <li>קומה :<span><?=$floorNumber?></span></li>
                        </ul>
                        <ul class="pdf-ul">
                            <li>מרחב :<span><?=html_entity_decode(str_replace("'"," ",$report->place->name.' ('.$report->place->custom_number.')'))?></span></li>
                            <li>מחסן/'ם :<span><?=$report->larder?></span></li>
                            <li>חניה/ות :<span><?=$report->parking?></span></li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
        <div class="pdf-content">
            <!--Additional details-->
            <div class="pdf-content-title">
                קריאת מונה
            </div>
            <?if(strlen($report->el_pic)):?>
                <div class="adit-dt">
                    <div class="adit-dt-info">
                        <p>מונה חשמל</p>
                    </div>
                    <div class="adit-dt-img">
                        <img src="/media/data/delivery-reports/<?=$report->id?>/<?=$report->el_pic?>" alt="">
                    </div>
                </div>
            <?endif;?>
            <?if(strlen($report->wm_pic)):?>
                <div class="adit-dt">
                    <div class="adit-dt-info">
                        <p>מונה מים</p>
                    </div>
                    <div class="adit-dt-img">
                        <img src="/media/data/delivery-reports/<?=$report->id?>/<?=$report->wm_pic?>" alt="">
                    </div>
                </div>
            <?endif?>
            <!--Additional details end-->
        </div>
        <div class="pdf-footer">
            <p>-<?=++$ii?>-</p>
        </div>
    </div>
</div>
<?endif;?>
<div class="pdf-main">
    <div class="pdf-padding">
        <div class="pdf-heading">
            <div class="pdf-logos">
                <?if($report->quality):?>
                    <img src="/media/img/new-images/quality.png" alt="" class="quality-mark">
                <?endif?>
                <img src="/media/img/new-images/q4b-logo.png" alt="" class="q4b-logo">
            </div>
            <div class="pdf-info">
                <div class="project-logo">
                    <img src="<?=$report->project->main_image->originalFilePath()?>" alt="<?=$project->name?>">
                </div>

                <div class="pdf-lists">
                    <div class="pdf-list-top">
                        <ul class="pdf-ul">
                            <li>פרוטוקול מסירה מס :<span><?=$report->id?></span></li>
                            <li>שם עובד :<span><?=$report->user->name?></span></li>
                        </ul>



                        <ul class="pdf-ul">
                            <li>תאריך :<span><?=date('d/m/Y',$report->created_at)?></span></li>
                        </ul>

                    </div>
                    <div class="pdf-list-bottom">

                        <ul class="pdf-ul">
                            <li>שם הפרויקט :<span><?=$report->project->name?><span></li>
                            <li>מבנה :<span><?=html_entity_decode($report->object->name)?></span></li>
                            <li>קומה :<span><?=$floorNumber?></span></li>
                        </ul>
                        <ul class="pdf-ul">
                            <li>מרחב :<span><?=html_entity_decode(str_replace("'"," ",$report->place->name.' ('.$report->place->custom_number.')'))?></span></li>
                            <li>מחסן/'ם :<span><?=$report->larder?></span></li>
                            <li>חניה/ות :<span><?=$report->parking?></span></li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
        <div class="pdf-content">
            <!--Approval document-->
            <div class="pdf-content-title">
                טופס טיולים/אישור יזם
            </div>
            <?foreach ($report->devAppFiles->find_all() as $file):?>
                <div class="pow-att">
                    <p class="pow-title">צילום אישור טופס טיולים/ יזם.</p>
                    <div class="img">

                        <img src="/<?=$file->path?>/<?=$file->name?>" alt="">
                    </div>
                </div>
            <?endforeach;?>
            <!--Approval document end-->
        </div>
        <div class="pdf-footer">
            <p>-<?=++$ii?>-</p>
        </div>
    </div>
</div>


<?if($report->quality_controls->count_all()):?>
    <?foreach ($report->quality_controls->find_all() as $qc):?>
<div class="pdf-main">
    <div class="pdf-padding">
        <div class="pdf-heading">
            <div class="pdf-logos">
                <?if($report->quality):?>
                    <img src="/media/img/new-images/quality.png" alt="" class="quality-mark">
                <?endif?>
                <img src="/media/img/new-images/q4b-logo.png" alt="" class="q4b-logo">
            </div>
            <div class="pdf-info">
                <div class="project-logo">
                    <img src="<?=$report->project->main_image->originalFilePath()?>" alt="<?=$project->name?>">
                </div>

                <div class="pdf-lists">
                    <div class="pdf-list-top">
                        <ul class="pdf-ul">
                            <li>פרוטוקול מסירה מס :<span><?=$report->id?></span></li>
                            <li>שם עובד :<span><?=$report->user->name?></span></li>
                        </ul>



                        <ul class="pdf-ul">
                            <li>תאריך :<span><?=date('d/m/Y',$report->created_at)?></span></li>
                        </ul>

                    </div>
                    <div class="pdf-list-bottom">

                        <ul class="pdf-ul">
                            <li>שם הפרויקט :<span><?=$report->project->name?><span></li>
                            <li>מבנה :<span><?=html_entity_decode($report->object->name)?></span></li>
                            <li>קומה :<span><?=$floorNumber?></span></li>
                        </ul>
                        <ul class="pdf-ul">
                            <li>מרחב :<span><?=html_entity_decode(str_replace("'"," ",$report->place->name.' ('.$report->place->custom_number.')'))?></span></li>
                            <li>מחסן/'ם :<span><?=$report->larder?></span></li>
                            <li>חניה/ות :<span><?=$report->parking?></span></li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
        <div class="pdf-content">
                <!--Qc reports-->
                <div class="pdf-content-title">
                    בקרת איכות :  <?=$qc->id?>
                </div>
                <div class="qc-rep">
                    <div class="qc-rep-lists">
                        <ul class="pdf-ul">
                            <li>חלל :<span><?=$qc->space->type->name?></span></li>
                            <li>מלאכה :<span><?=$qc->craft->name?></span></li>
                        </ul>
                        <ul class="pdf-ul">
                            <li>סטטוס :<span><?=__($qc->status)?></span></li>
                            <li>באחריות :<span><?=$qc->profession->name?></span></li>
                        </ul>
                    </div>
                    <div class="qc-rep-desc">
                        <span>תיאור:</span>
                        <p><?=$qc->description?></p>
                    </div>
                    <div class="qc-rep-title">
                        תמונות מצורפות
                    </div>
                    <div class="qc-rep-images">
                        <?foreach ($qc->images->find_all() as $img):?>
                            <div class="qc-rep-img">
                                <div class="img-desc"><p><span><?=$img->original_name?></span>(uploaded: <?=date('d/m/Y H:i',$img->created_at)?>)</p></div>
                                <img src="<?=URL::withLang($img->originalFilePath(),Language::getDefault()->iso2,'https')?>" alt="<?=$img->original_name?>">
<!--                                <img src="--><?//=$img->originalFilePath()?><!--" alt="--><?//=$img->original_name?><!--">-->
                            </div>
                        <?endforeach;?>
                    </div>
                </div>
                <!--Qc reports end-->
        </div>
        <div class="pdf-footer">
            <p>-<?=++$ii?>-</p>
        </div>
    </div>
</div>
    <?endforeach;?>
<?endif;?>

<?if($report->transferableItems->count_all()):?>
<div class="pdf-main">
    <div class="pdf-padding">
        <div class="pdf-heading">
            <div class="pdf-logos">
                <?if($report->quality):?>
                    <img src="/media/img/new-images/quality.png" alt="" class="quality-mark">
                <?endif?>
                <img src="/media/img/new-images/q4b-logo.png" alt="" class="q4b-logo">
            </div>
            <div class="pdf-info">
                <div class="project-logo">
                    <img src="<?=$report->project->main_image->originalFilePath()?>" alt="<?=$project->name?>">
                </div>

                <div class="pdf-lists">
                    <div class="pdf-list-top">
                        <ul class="pdf-ul">
                            <li>פרוטוקול מסירה מס :<span><?=$report->id?></span></li>
                            <li>שם עובד :<span><?=$report->user->name?></span></li>
                        </ul>



                        <ul class="pdf-ul">
                            <li>תאריך :<span><?=date('d/m/Y',$report->created_at)?></span></li>
                        </ul>

                    </div>
                    <div class="pdf-list-bottom">

                        <ul class="pdf-ul">
                            <li>שם הפרויקט :<span><?=$report->project->name?><span></li>
                            <li>מבנה :<span><?=html_entity_decode($report->object->name)?></span></li>
                            <li>קומה :<span><?=$floorNumber?></span></li>
                        </ul>
                        <ul class="pdf-ul">
                            <li>מרחב :<span><?=html_entity_decode(str_replace("'"," ",$report->place->name.' ('.$report->place->custom_number.')'))?></span></li>
                            <li>מחסן/'ם :<span><?=$report->larder?></span></li>
                            <li>חניה/ות :<span><?=$report->parking?></span></li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
        <div class="pdf-content">

            <!--final stage 1 -->
            <div class="pdf-content-title">
                נספח
            </div>
            <div class="fnl-stg">
                <div class="fnl-stg-title">
                    רשימת מפתחות וציוד למסירה
                </div>

                    <table class="fnl-stg-table">
                        <thead>
                        <tr>
                            <th>ערך</th>
                            <th>כמות</th>
                            <th>הערה</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?foreach ($report->transferableItems->find_all() as $item):?>
                            <tr>
                                <td><?=$item->text?></td>
                                <td><?=$item->quantity?></td>
                                <td><?=$item->comment?></td>
                            </tr>
                        <?endforeach?>
                        </tbody>
                    </table>

                <div class="fnl-stg-desc">
                    אחריות על המוצרים הינה כפופה לשימוש נכון ותקין במוצרים, תוכלו למצוא הור
                    הנני מאשר קבלת דפי הסבר על מערכת מיזוג האוויר, שלטים והסבר בנושא ניקוי מסננים. בנוסף, הנני מאשר קבלת חוברת
                    תחזוקה.
                </div>
            </div>
            <!--final stage 1 end -->
        </div>
        <div class="pdf-footer">
            <p>-<?=++$ii?>-</p>
        </div>
    </div>
</div>
<?endif?>
<?if($report->reserveMaterials->count_all()):?>
<div class="pdf-main">
    <div class="pdf-padding">
        <div class="pdf-heading">
            <div class="pdf-logos">
                <?if($report->quality):?>
                    <img src="/media/img/new-images/quality.png" alt="" class="quality-mark">
                <?endif?>
                <img src="/media/img/new-images/q4b-logo.png" alt="" class="q4b-logo">
            </div>
            <div class="pdf-info">
                <div class="project-logo">
                    <img src="<?=$report->project->main_image->originalFilePath()?>" alt="<?=$project->name?>">
                </div>

                <div class="pdf-lists">
                    <div class="pdf-list-top">
                        <ul class="pdf-ul">
                            <li>פרוטוקול מסירה מס :<span><?=$report->id?></span></li>
                            <li>שם עובד :<span><?=$report->user->name?></span></li>
                        </ul>



                        <ul class="pdf-ul">
                            <li>תאריך :<span><?=date('d/m/Y',$report->created_at)?></span></li>
                        </ul>

                    </div>
                    <div class="pdf-list-bottom">

                        <ul class="pdf-ul">
                            <li>שם הפרויקט :<span><?=$report->project->name?><span></li>
                            <li>מבנה :<span><?=html_entity_decode($report->object->name)?></span></li>
                            <li>קומה :<span><?=$floorNumber?></span></li>
                        </ul>
                        <ul class="pdf-ul">
                            <li>מרחב :<span><?=html_entity_decode(str_replace("'"," ",$report->place->name.' ('.$report->place->custom_number.')'))?></span></li>
                            <li>מחסן/'ם :<span><?=$report->larder?></span></li>
                            <li>חניה/ות :<span><?=$report->parking?></span></li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
        <div class="pdf-content">

            <!--final stage 2 -->
            <div class="pdf-content-title">
                נספח
            </div>
            <div class="fnl-stg">
                <div class="fnl-stg-title">
                    רשימת קרמיקה וריצוף
                </div>

                    <table class="fnl-stg-table2">
                        <thead>
                        <tr>
                            <th>ערך</th>
                            <th>סוג</th>
                            <th>כמות</th>
                            <th>הערה</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?foreach ($report->reserveMaterials->find_all() as $item):?>
                            <tr>
                                <td><?=$item->text?></td>
                                <td><?=$item->size?></td>
                                <td><?=$item->quantity?></td>
                                <td><?=$item->comment?></td>
                            </tr>
                        <?endforeach;?>
                        </tbody>
                    </table>

                <div class="fnl-stg-desc">
                    .1 הנני מתחייב לאחסן ולשמור את הריצוף והקרמיקות הרשומים מטה, וזאת לתקופה של 7 שנים.
                    <br>.2	היה ולא אשמור ו/או אשתמש בריצוף / קרמיקה זו, לא תהינה לי כל טענה כלפי החברה בנוגע להתאמת צבעים ו/ או התאמת
                    דגמים.
                </div>
            </div>
            <!--final stage 2 end-->
        </div>
        <div class="pdf-footer">
            <p>-<?=++$ii?>-</p>
        </div>
    </div>
</div>
<?endif?>
<div class="pdf-main">
    <div class="pdf-padding">
        <div class="pdf-heading">
            <div class="pdf-logos">
                <?if($report->quality):?>
                    <img src="/media/img/new-images/quality.png" alt="" class="quality-mark">
                <?endif?>
                <img src="/media/img/new-images/q4b-logo.png" alt="" class="q4b-logo">
            </div>
            <div class="pdf-info">
                <div class="project-logo">
                    <img src="<?=$report->project->main_image->originalFilePath()?>" alt="<?=$project->name?>">
                </div>

                <div class="pdf-lists">
                    <div class="pdf-list-top">
                        <ul class="pdf-ul">
                            <li>פרוטוקול מסירה מס :<span><?=$report->id?></span></li>
                            <li>שם עובד :<span><?=$report->user->name?></span></li>
                        </ul>



                        <ul class="pdf-ul">
                            <li>תאריך :<span><?=date('d/m/Y',$report->created_at)?></span></li>
                        </ul>

                    </div>
                    <div class="pdf-list-bottom">

                        <ul class="pdf-ul">
                            <li>שם הפרויקט :<span><?=$report->project->name?><span></li>
                            <li>מבנה :<span><?=html_entity_decode($report->object->name)?></span></li>
                            <li>קומה :<span><?=$floorNumber?></span></li>
                        </ul>
                        <ul class="pdf-ul">
                            <li>מרחב :<span><?=html_entity_decode(str_replace("'"," ",$report->place->name.' ('.$report->place->custom_number.')'))?></span></li>
                            <li>מחסן/'ם :<span><?=$report->larder?></span></li>
                            <li>חניה/ות :<span><?=$report->parking?></span></li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
        <div class="pdf-content">

            <!--final stage 3 -->
            <div class="pdf-content-title">
                נספח
            </div>
            <div class="signature">
                <div class="signature-desc">
                    <span>תיאור:</span>
                    <p><?=$report->comment?></p>
                </div>
                <div class="signature-title">
                    הסכמה:
                </div>
                <div class="signature-content"><?=$report->s_text_type_3?></div>
                <div class="signature-signature">
                    <span>חתימת הלקוח :</span>

                    <div class="img">
                        <img src="/media/data/delivery-reports/<?=$report->id?>/<?=$report->signature?>" alt="">
                    </div>
                </div>
            </div>
            <!--final stage 3 end-->
        </div>
        <div class="pdf-footer">
            <p>-<?=++$ii?>-</p>
        </div>
    </div>
</div>
<script>
    (function(){
        if(window.opener) {
            //window.opener.csrf = document.querySelector(Q4U.options.csrfTokenSelector).content;
            window.print();
            setTimeout(function () {
                window.close();
            }, 1500);
        }
    })()
</script>
</body>
</html>

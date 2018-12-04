
<?//$_SITE_URL = 'https://q4b.horizondvp.org';

$lang = Language::getCurrent()->iso2;
?>
<!DOCTYPE html>
<html lang="en" style="width: 100%; height: 100%; margin: 0; padding: 0; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body class="email-page" style="margin: 0; padding: 30px 15px; background-image: url('<?=$_SITE_URL?>/media/img/login_bg.jpg'); background-repeat: no-repeat; background-size: contain; background-position: center; background-color: #f2f3f8;  position: relative; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">

    <div class="q4-email-wrapper" style="<?=($lang == 'he' ? 'direction:rtl' : 'direction:ltr')?>; max-width: 615px; width: 470px; margin: 0px auto 20px; padding: 20px 0; color: #000; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">

        <table style="width:100%; margin: 0 auto;">
            <tr>
                <td>
                    <div class="q4-email-header" style="height: 120px; width: 100%; text-align: left; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                        <img src="<?=$_SITE_URL?>/media/img/email-logo.png" alt="logo" style="width: 191px; height: 93px; display: inline-block; vertical-align: middle; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="q4-email-body-mes" style="mrgin:10px 0; color: #494949;font-size: 14px; font-weight: normal; font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif; line-height: 1; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">

                     <?$desc = explode("\n",$message);
                     foreach ($desc as $line) {?>
                     <p><?=$line?></p>
                     <?}?>
                 </div>
             </td>
         </tr>
         <tr>
            <td>
                <div class="q4-email-body" style="background: white; width: 100%; padding: 35px 15px 30px 15px; margin-bottom: 15px; border: 1px solid #d4e1ea; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">

                    <div class="email-row" style="overflow: hidden;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                        <div class="email-col-12" style="width: 100%; padding: 15px;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                            <label class="table-label" style="display: block; margin-bottom: 7px;color: #005c87;font-size: 16px;font-weight: normal;font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                                <?=__('Project name')?>
                            </label>
                            <span class="q4-form-input" style="display: block;height: 30px;line-height: 30px;padding: 0 6px 0 6px; -webkit-border-radius: 0; -moz-border-radius: 0;-ms-border-radius: 0;border-radius: 0;border: 1px solid #d4e1ea;color: #708492;font-size: 15px;font-weight: normal;font-style: normal;font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                                <?=$item->project->name?>
                            </span>
                        </div>
                    </div>
                    <div class="email-row" style="overflow: hidden;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                        <div class="email-col-12" style="width: 100%; padding: 15px;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                            <label class="table-label" style="display: block; margin-bottom: 7px;color: #005c87;font-size: 16px;font-weight: normal;font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                                <?=__('Structure')?>
                            </label>
                            <span class="q4-form-input" style="display: block;height: 30px;line-height: 30px;padding: 0 6px 0 6px; -webkit-border-radius: 0; -moz-border-radius: 0;-ms-border-radius: 0;border-radius: 0;border: 1px solid #d4e1ea;color: #708492;font-size: 15px;font-weight: normal;font-style: normal;font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                             <?=$item->object->type->name.'-'.$item->object->name?>
                         </span>
                     </div>
                 </div>

                 <div class="email-row" style="overflow: hidden;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                    <div class="email-col-12" style="width: 100%; padding: 15px;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                        <label class="table-label" style="display: block; margin-bottom: 7px;color: #005c87;font-size: 16px;font-weight: normal;font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                            <?=__('Craft')?>
                        </label>
                        <span class="q4-form-input" style="display: block; height: 30px;line-height: 30px; padding: 0 6px 0 6px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; -webkit-border-radius: 0; -moz-border-radius: 0;-ms-border-radius: 0;border-radius: 0;border: 1px solid #d4e1ea;color: #708492;font-size: 15px;font-weight: normal;font-style: normal;font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                            <?=$item->craft->name?>
                        </span>
                    </div>

                </div>

                <div class="email-row" style="overflow: hidden;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">


                    <div class="email-col-6" style="width: 50%; float: left; padding: 15px;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                        <label class="table-label" style="display: block; margin-bottom: 7px;color: #005c87;font-size: 16px;font-weight: normal;font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                            <?=__('Stage')?>
                        </label>
                        <span class="q4-form-input" style="display: block;height: 30px;line-height: 30px;padding: 0 6px 0 6px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; -webkit-border-radius: 0; -moz-border-radius: 0;-ms-border-radius: 0;border-radius: 0;border: 1px solid #d4e1ea;color: #708492;font-size: 15px;font-weight: normal;font-style: normal;font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                            <?=__($item->project_stage)?>
                        </span>
                    </div>

                    <div class="email-col-6" style="width: 50%; float: left; padding: 15px;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                        <label class="table-label" style="display: block; margin-bottom: 7px;color: #005c87;font-size: 16px;font-weight: normal;font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                            <?=__('Floor')?>
                        </label>
                        <span class="q4-form-input" style="display: block;height: 30px;line-height: 30px;padding: 0 6px 0 6px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; -webkit-border-radius: 0; -moz-border-radius: 0;-ms-border-radius: 0;border-radius: 0;border: 1px solid #d4e1ea;color: #708492;font-size: 15px;font-weight: normal;font-style: normal;font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                           <?=$item->place->floor->number?>
                       </span>
                   </div>


               </div>
               <div class="email-row" style="overflow: hidden;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">


                <div class="email-col-12" style="width: 100%; padding: 15px;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                    <label class="table-label" style="display: block; margin-bottom: 7px;color: #005c87;font-size: 16px;font-weight: normal;font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                        <?=__('Element')?>
                    </label>
                    <span class="q4-form-input" style="display: block;height: 30px;line-height: 30px;padding: 0 6px 0 6px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; -webkit-border-radius: 0; -moz-border-radius: 0;-ms-border-radius: 0;border-radius: 0;border: 1px solid #d4e1ea;color: #708492;font-size: 15px;font-weight: normal;font-style: normal;font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                        <?=$item->place->name?>
                    </span>
                </div>

            </div>
            <div class="email-row" style="overflow: hidden;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                <div class="email-col-6" style="width: 50%; float: left; padding: 15px;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                    <label class="table-label" style="display: block; margin-bottom: 7px;color: #005c87;font-size: 16px;font-weight: normal;font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                        <?=__('Element number')?>
                    </label>
                    <span class="q4-form-input" style="display: block;height: 30px;line-height: 30px;padding: 0 6px 0 6px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; -webkit-border-radius: 0; -moz-border-radius: 0;-ms-border-radius: 0;border-radius: 0;border: 1px solid #d4e1ea;color: #708492;font-size: 15px;font-weight: normal;font-style: normal;font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                        <?=$item->place->number?>
                    </span>
                </div>
            </div>

            <div class="email-row" style="overflow: hidden;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">

                <div class="email-col-12" style="width: 100%; padding: 15px;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                    <label class="table-label" style="display: block; margin-bottom: 7px;color: #005c87;font-size: 16px;font-weight: normal;font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                        <?=__('Space')?>
                    </label>
                    <span class="q4-form-input" style="display: block;height: 30px;line-height: 30px;padding: 0 6px 0 6px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; -webkit-border-radius: 0; -moz-border-radius: 0;-ms-border-radius: 0;border-radius: 0;border: 1px solid #d4e1ea;color: #708492;font-size: 15px;font-weight: normal;font-style: normal;font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                       Space1:<?=$item->space->desc?>
                   </span>
               </div>


           </div>
           <div class="email-row" style="overflow: hidden;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
            <div class="email-col-6" style="width: 50%; float: left; padding: 15px;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                <label class="table-label" style="display: block; margin-bottom: 7px;color: #005c87;font-size: 16px;font-weight: normal;font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                    <?=__('Status')?>
                </label>
                <span class="q4-form-input" style="display: block;height: 30px;line-height: 30px;padding: 0 6px 0 6px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; -webkit-border-radius: 0; -moz-border-radius: 0;-ms-border-radius: 0;border-radius: 0;border: 1px solid #d4e1ea;color: #708492;font-size: 15px;font-weight: normal;font-style: normal;font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                   <?=__($item->status)?>
               </span>
           </div>

           <div class="email-col-6" style="width: 50%; float: left; padding: 15px;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
            <label class="table-label" style="display: block; margin-bottom: 7px;color: #005c87;font-size: 16px;font-weight: normal;font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                <?=__('Due Date')?>
            </label>
            <span class="q4-form-input" style="display: block;height: 30px;line-height: 30px;padding: 0 6px 0 6px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; -webkit-border-radius: 0; -moz-border-radius: 0;-ms-border-radius: 0;border-radius: 0;border: 1px solid #d4e1ea;color: #708492;font-size: 15px;font-weight: normal;font-style: normal;font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                <?=date('d/m/Y', $item->due_date)?>
            </span>
        </div>

    </div>

    <div class="email-row" style="overflow: hidden;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">

        <div class="email-col-12" style="width: 100%; padding: 15px;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
            <label class="table-label" style="display: block; margin-bottom: 7px;color: #005c87;font-size: 16px;font-weight: normal;font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                <?=__('Responsible profession')?>
            </label>
            <span class="q4-form-input" style="display: block;height: 30px;line-height: 30px;padding: 0 6px 0 6px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; -webkit-border-radius: 0; -moz-border-radius: 0;-ms-border-radius: 0;border-radius: 0;border: 1px solid #d4e1ea;color: #708492;font-size: 15px;font-weight: normal;font-style: normal;font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                <?=$item->profession->name?>
            </span>
        </div>

    </div>

    <?if($item->plan->status):?>

        <div class="email-row" style="overflow: hidden;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
            <div class="email-col-12" style="padding: 0 15px;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                <span class="email-plan-title" style="display: block;height: 30px;line-height: 30px;padding: 0 6px 0 6px; color: #005c87; font-size: 16px;font-weight: normal;font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif; -webkit-border-radius: 0; -moz-border-radius: 0;-ms-border-radius: 0;border-radius: 0;">
                   <?=__('Plan')?>
               </span>
           </div>
       </div>
       <div class="email-row" style="overflow: hidden;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">

        <div class="email-qc-plan-name" style="background: #f2faff; border: 1px solid #d4e1ea; margin: 0 15px; padding: 12px 0; position: relative; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">

            <label class="table-label" style="display: block; margin-bottom: 10px; padding: 0 15px 0 15px;  color: #005c87;font-size: 16px;font-weight: normal;font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                <?=__('Plan name')?>: <?=$item->plan->file() ? $item->plan->file()->getName() : $item->plan->name?>
            </label>

            <div class="email-row" style="overflow: hidden;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                <div class="email-col-3" style="width: 28%; float: left; padding: 15px;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                    <label class="table-label" style="display: block; margin-bottom: 7px;color: #005c87;font-size: 16px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-weight: normal;font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                        <?=__('Edition')?>
                    </label>
                    <span class="q4-form-input" style="display: block;height: 30px;line-height: 30px;padding: 0 6px 0 6px; -webkit-border-radius: 0; -moz-border-radius: 0;-ms-border-radius: 0;border-radius: 0;border: 1px solid #d4e1ea;color: #708492;font-size: 15px;font-weight: normal;font-style: normal;font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                       <?=$item->plan->edition?>
                   </span>
               </div>
               <div class="email-col-3" style="width: 30%; float: left; padding: 15px;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                <label class="table-label" style="display: block; margin-bottom: 7px;color: #005c87;font-size: 16px;font-weight: normal;font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                    <?=__('Date')?>
                </label>
                <span class="q4-form-input" style="display: block;height: 30px;line-height: 30px;padding: 0 6px 0 6px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; -webkit-border-radius: 0; -moz-border-radius: 0;-ms-border-radius: 0;border-radius: 0;border: 1px solid #d4e1ea;color: #708492;font-size: 15px;font-weight: normal;font-style: normal;font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                    <?=$item->plan->date ? date('d/m/Y', $item->plan->date) : ''?>
                </span>
            </div>
            <div class="email-col-5" style="width: 41.66%; float: left; padding: 15px;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                <label class="table-label" style="display: block; margin-bottom: 7px;color: #005c87;font-size: 16px;font-weight: normal;font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                    <?=__('Status')?>
                </label>
                <span class="q4-form-input" style="display: block;height: 30px;line-height: 30px;padding: 0 6px 0 6px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; -webkit-border-radius: 0; -moz-border-radius: 0;-ms-border-radius: 0;border-radius: 0;border: 1px solid #d4e1ea;color: #708492;font-size: 15px;font-weight: normal;font-style: normal;font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                    <?=__($item->plan->status)?>
                </span>
            </div>
        </div>

        <div>
            <span class="format-title" style="display: inline-block;vertical-align: middle; margin-left: 15px; color: #005c87;font-size: 16px;font-weight: normal;font-style: normal;font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;">
                <?=__('View format')?>:
            </span>
            <?$file = $item->plan->files->where('status','=',Enum_FileStatus::Active)->find()?>
            <a href="<?=$_SITE_URL.$file->originalFilePath()?>" style="display: inline-block; vertical-align: middle; margin-left: 15px;">
                <img src="<?=$_SITE_URL?>/media/img/choose-format/format-pdf.png" alt="format-pdf" style="display: inline-block;vertical-align: middle; margin-left: 20px;">
            </a>
        </div>
    </div><!--.email-qc-plan-name-->
</div>

<?endif;?>
<?if(count($item->tasks) > 0):?>
    <div class="email-row" style="overflow: hidden;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">

        <div class="email-tasks-description-box" style="padding-top: 5px;">
            <label class="table-label" style="display: block; margin-top: 15px; margin-bottom: 7px;
                padding: 0 15px; color: #005c87;font-size: 16px;font-weight: normal;
                font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;
                box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                <?=__('Tasks List')?>
            </label>
            <ul class="email-qc-tasks-list" style="margin: 0 15px; padding: 0; font-size: 0;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                <?foreach($item->tasks->find_all() as $task):?>

                    <li style="position: relative; vertical-align: top; display: inline-block; width: 100%; min-height: 85px; margin-bottom: 15px; margin-left: 0; background: #f2faff; border: 1px solid #d4e1ea; padding: 7px 6px 7px 6px; cursor: pointer; font-size: 14px; font-weight: normal; font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                        <label class="table-label" style="margin-bottom: 10px; color: #1ebae5; font-size: 16px; font-weight: normal; font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;">
                            <?=__('Task')?> <?=$task->id?>
                        </label>
                        <?$desc = explode("\n",$task->name);
                        foreach ($desc as $line) {?>
                        <p style="color: #708492; font-size: 15px; font-weight: normal; font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif; line-height: 18px;">
                            <?=$line?>
                        </p>
                        <?}?>
                    </li>
                    <?endforeach;?>
            </ul>

        </div>
    </div>
    <?endif;?>
    <?if($item->status == 'invalid'):?>
        <div class="email-row" style="overflow: hidden; margin: 0 15px; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
            <label class="table-label" style="display: block; margin-top: 5px; margin-bottom: 3px;color: #005c87;font-size: 16px;font-weight: normal;font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                <?=__('Conditions')?>
            </label>

            <div class="email-row" style="overflow: hidden;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                <div class="email-col-6" style="width: 50%; float: left; padding: 15px;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                    <label class="table-label" style="display: block; margin-bottom: 7px;color: #005c87;font-size: 16px;font-weight: normal;font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                        <?=__('Severity Level')?>
                    </label>
                    <span class="q4-form-input" style="display: block;height: 30px;line-height: 30px;padding: 0 6px 0 6px; -webkit-border-radius: 0; -moz-border-radius: 0;-ms-border-radius: 0;border-radius: 0;border: 1px solid #d4e1ea;color: #708492;font-size: 15px;font-weight: normal;font-style: normal;font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                       <?=__($item->severity_level)?>
                   </span>
               </div>
               <div class="email-col-6" style="width: 50%; float: left; padding: 15px;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                <label class="table-label" style="display: block; margin-bottom: 7px;color: #005c87;font-size: 16px;font-weight: normal;font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                    <?=__('Conditions List')?>
                </label>
                <span class="q4-form-input" style="display: block;height: 30px;line-height: 30px;padding: 0 6px 0 6px; -webkit-border-radius: 0; -moz-border-radius: 0;-ms-border-radius: 0;border-radius: 0;border: 1px solid #d4e1ea;color: #708492;font-size: 15px;font-weight: normal;font-style: normal;font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                    <?=__( $item->condition_list)?>
                </span>
            </div>
        </div>

    </div>

    <?endif;?>


    <?if(count($item->images)):?>
        <div class="email-row" style="margin: 0 15px;">
            <label class="table-label" style="display: block; margin-bottom: 3px;color: #005c87;font-size: 16px;font-weight: normal;font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                <?=__('Images List')?>
            </label>

            <div class="email-image-lists" style="box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">

                <?foreach ($item->images->where('status','=',Enum_FileStatus::Active)->find_all() as $image):?>
                    <div class="email-image-list-item" style="margin-bottom: 15px;">

                        <span class="email-image-title" style="display: inline-block; color: #1ebae5; font-size: 15px; font-weight: bold; font-style: normal; font-weight: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif; margin-bottom: 3px; padding-left: 3px;">
                            <?=$image->original_name?>
                            <span class="email-image-date" style="color: #708492;">(<?=__('uploaded')?>: <?=date('d.m.Y',$image->created_at)?>)</span>
                        </span>

                        <div class="email-image-list-pic" style="background-color: #d4e1ea; height: 400px; line-height: 400px;  text-align: center; background: #ffffff; border: 1px solid #ccc;">
                            <img src="<?=$_SITE_URL.$image->originalFilePath()?>" style="display: inline-block; vertical-align: middle; width: auto; height: auto; max-height: 100%; max-width: 100%; border: 1px solid #d4e1ea;">
                        </div>
                    </div>
                    <?endforeach?>
                </div>

            </div>
            <?endif;?>

            <?if($item->description):?>
                <div class="email-description-box" style="margin: 0 15px;">

                    <label class="table-label" style="display: block; margin-bottom: 7px;color: #005c87;font-size: 16px;font-weight: normal;font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                        <?=__('Description')?>
                    </label>
                    <div style="border: 1px solid #d4e1ea; padding: 10px 6px 10px 6px;
                    color: #8ea1ae; font-size: 14px; font-weight: normal; font-style: italic;
                    font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;
                    box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;"><?=trim($item->description)?>

                </div>
                <?endif;?>
            </div>

            <div class="email-status-box" style="margin: 15px 15px;">
                <?if($approveUsr->name):?>
                    <div class="email-status-row" style="margin-bottom: 7px;">
                        <span class="email-action" style="margin: 0 3px; color: #003a63; font-size: 14px; font-weight: normal; font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;">
                         <?=__('Viewed by')?>:
                     </span>
                     <span class="email-action-status" style="color: #003a63; font-size: 14px; font-weight: normal; font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;"><?=$approveUsr->name?>&lrm;
                        <span class="d-color" style="color: #8ea1ae;">(<?=date('d.m.Y H:ia',$item->approved_at)?>)&lrm;</span>
                    </span>
                </div>
                <?endif?>
                <div class="email-status-row" style="margin-bottom: 7px;">
                    <span class="email-action" style="margin: 0 3px; color: #003a63; font-size: 14px; font-weight: normal; font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;">
                     <?=__('Status')?>:
                 </span>
                 <span class="email-action-status" style="color: #003a63; font-size: 14px; font-weight: bold; font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;"><?=__($item->approval_status)?></span>
             </div>
             <?if($createUsr->name):?>
                <div class="email-status-row" style="margin-bottom: 7px;">
                    <span class="modal-details-action" style="margin: 0 3px; color: #003a63; font-size: 14px; font-weight: normal; font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                        <?=__('Created by')?> :
                    </span>
                    <span class="modal-details-action-status" style="color: #003a63; font-size: 14px; font-weight: normal; font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                        <?=$createUsr->name?> &lrm;
                        <span class="d-color" style="color: #8ea1ae; font-size: 14px; font-weight: normal; font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;">(<?=date('d.m.Y H:ia',$item->created_at)?>)&lrm;</span></span>
                    </div>
                    <?endif?>
                    <?if($updateUsr->name):?>
                        <div class="email-status-row" style="margin-bottom: 7px;">
                            <span class="email-action" style="margin: 0 3px; color: #003a63; font-size: 14px; font-weight: normal; font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                             <?=__('Updated by')?>:
                         </span>
                         <span class="email-action-status"  style="color: #003a63; font-size: 14px; font-weight: normal; font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                            <?=$updateUsr->name?> &lrm;
                            <span class="d-color" style="color: #8ea1ae; font-size: 14px; font-weight: normal; font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif;  box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">(<?=date('d.m.Y H:ia',$item->updated_at)?>)&lrm;</span>
                        </span>
                    </div>
                    <?endif?>

                </div>
                <div class="q4-email-footer">

                    <span style="display: block; margin: 0 15px 10px 15px; color: #494949; font-size: 14px; font-weight: normal; font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif; line-height: 1;">
                        <span style="display: inline-block; vertical-align: middle;"><?=__('For customer support')?>: </span>
                        <a href="mailto:support@sh-av.co.il" style="display: inline-block; vertical-align: middle; margin-left: 5px; color: #1ebae5; text-decoration: underline; font-weight: bold;">support@sh-av.co.il</a>
                    </span>
                    <span style="display: block; margin: 0 15px 10px 15px; color: #494949; font-size: 14px; font-weight: normal; font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif; line-height: 1;">
                        <?=__('For contact us')?>: <a href="mailto:support@sh-av.co.il" style="color: #1ebae5; text-decoration: underline; font-weight: bold;">support@sh-av.co.il</a>
                    </span>
                    <span style="display: block; margin: 0 15px 10px 15px; color: #494949; font-size: 14px; font-weight: normal; font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif; line-height: 1;">
                        <?=__('Sent by')?>: <a href="mailto:<?=$user['email']?>" style="color: #1ebae5; text-decoration: underline; font-weight: bold;"><?=$user['name']?></a>
                    </span>
                </div>

                <div style="display: block; margin: 20px 15px; line-height: 1;">
                    <a href="https://qforb.net" target="_blank" style="display: block; height: 34px; line-height: 34px; background: #1ebae5; color: #ffffff; text-align: center; text-decoration: none; font-size: 15px; font-weight: normal; font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif; -webkit-border-radius: 5px;-moz-border-radius: 5px; -ms-border-radius: 5px; border-radius: 5px;">
                        <?=__('Login')?>
                    </a>
                </div>

            </div>

        </td>
    </tr>
</table>

</div>
</body>
</html>

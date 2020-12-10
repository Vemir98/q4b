<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 31.05.2017
 * Time: 12:37
 */

$lang = Language::getCurrent()->iso2;

?>

<body class="email-page" style=" margin: 0; padding: 30px 15px; background-image: url(https://qforb.net/media/img/login_bg.jpg); background-repeat: no-repeat; background-size: contain; background-position: center; background-color: #f2f3f8; position: relative; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">

<div class="q4-email-wrapper" style="direction:<?=Language::getCurrent()->direction?>; max-width: 615px; margin: 35px auto 20px; padding: 50px 0px; color: #000;
    box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
    <table style="margin: 0 auto;">
        <tr>
            <td>
                <div class="q4-email-header" style="height: 120px; width: 100%; text-align: left; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                    <?if(!empty($image)):?>
                        <img src="<?=$image?>" alt="logo" style="margin: auto; height: 93px; display: block; vertical-align: middle; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                    <?else:?>
                    <img src="https://qforb.net/media/img/email-logo.png" alt="logo" style="width: 191px; height: 93px; display: inline-block; vertical-align: middle; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                    <?endif;?>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="q4-email-body" style="background: white; width: 100%; padding: 35px 25px 30px 25px; margin-bottom: 15px; border: 1px solid #d4e1ea; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">

                    <div>
                        <h1 style="color: #494949;font-size: 24px; font-weight: normal; font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif; line-height: 1;"><?=__('Reports')?></h1>
                        <h2><?=__('To view, click on the protocol:')?></h2>
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <?foreach ($reports as $report):?>
                            <li><a style="color: #1ebae5;" href="<?='https://qforb.net/media/data/delivery-reports/'.$report->id.'/file.pdf'?>"><?=$report->id?> - <?=str_replace("'"," ",$report->place->name.' ('.$report->place->custom_number.')')?> - <?=$report->object->name?></a></li>
                            <?endforeach?>
                        </ul>
                    </div>
                     <div class="q4-email-body-mes" style="mrgin:10px 0; color: #494949;font-size: 14px; font-weight: normal; font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif; line-height: 1; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">

                         <?$desc = explode("\n",$message);
                            foreach ($desc as $line) {?>
                                <p><?=$line?></p>
                        <?}?>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="q4-email-footer">
                        <span style="display: block; margin: 0 0 10px 5px; color: #494949; font-size: 14px; font-weight: normal; font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif; line-height: 1;">
                            <?=__('For customer support')?>: <a href="mailto:support@sh-av.co.il" style="color: #1ebae5; text-decoration: underline; font-weight: bold;">support@sh-av.co.il</a>
                        </span>
                    <span style="display: block; margin: 0 0 10px 5px; color: #494949; font-size: 14px; font-weight: normal; font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif; line-height: 1;">
                            <?=__('For contact us')?>: <a href="mailto:support@sh-av.co.il" style="color: #1ebae5; text-decoration: underline; font-weight: bold;">support@sh-av.co.il</a>
                        </span>
                  <span style="display: block; margin: 0 0 0 5px; color: #494949; font-size: 14px; font-weight: normal; font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif; line-height: 1;">
                            <?=__('Sent by')?>: <a href="mailto:<?=$user['email']?>" style="color: #1ebae5; text-decoration: underline; font-weight: bold;"><?=$user['name']?></a>
                        </span>
                </div>
            </td>
        </tr>
    </table>
</div>
</body>


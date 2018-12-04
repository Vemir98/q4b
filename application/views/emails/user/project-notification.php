<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 18.11.2016
 * Time: 6:28
 */

$lang = Language::getCurrent()->iso2;
?>
<body class="email-page" style=" margin: 0; padding: 30px 15px; background-image: url(https://qforb.net/media/img/login_bg.jpg); background-repeat: no-repeat; background-size: contain; background-position: center; background-color: #f2f3f8; position: relative; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">

<div class="q4-email-wrapper" style="direction:<?=Language::getCurrent()->direction?>"; max-width: 615px; margin: 35px auto 20px; color: #000; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
    <table style="margin: 0 auto;">
        <tr>
            <td>
                <div class="q4-email-header" style="height: 120px; width: 100%; text-align: left; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                    <img src="https://qforb.net/media/img/email-logo.png" alt="logo" style="width: 191px; height: 93px; display: inline-block; vertical-align: middle; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="q4-email-body" style="background: white; width: 100%; padding: 35px 25px 30px 25px; margin-bottom: 15px; border: 1px solid #d4e1ea; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                    <div>

                        <?
                        //echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r($projects); echo "</pre>"; ?>
                        <?foreach ($projects as $project): ?>
                            <span style="display: block; color: #494949; font-size: 16px; font-weight: normal; font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif; line-height: 1; margin-bottom: 7px;">
                                <? if(Language::getCurrent()->direction == 'rtl'):?>
                                    <?=__('were created new quality control forms')?>
                                    <?=$project['name']?>,
                                    <?=__('Today in the project')?>
                                <?else:?>
                                    <?=__('Today in the project')?>
                                    <?=$project['name']?>,
                                    <?=__('were created new quality control forms')?>
                                <?endif?>
                            </span>
                        <?endforeach; ?>
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
                    <span style="display: block; margin: 0 0 0 5px; color: #494949; font-size: 14px; font-weight: normal; font-style: normal; font-family: 'proxima_nova_rgregular', Arial, Helvetica, sans-serif; line-height: 1;">
                            <?=__('For contact us')?>: <a href="mailto:support@sh-av.co.il" style="color: #1ebae5; text-decoration: underline; font-weight: bold;">support@sh-av.co.il</a>
                        </span>
                </div>
            </td>
        </tr>
    </table>
</div>
</body>

<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 21.11.2016
 * Time: 15:02
 */
?>
<!DOCTYPE html>
<html lang="<?=Language::getCurrent()->iso2?>" class="<?=Language::getCurrent()->direction?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 Permission Denied</title>
    <style>
        html, body, div, span, applet, object, iframe,
        h1, h2, h3, h4, h5, h6, p, blockquote, pre,
        a, abbr, acronym, address, big, cite, code,
        del, dfn, em, img, ins, kbd, q, s, samp,
        small, strike, strong, sub, sup, tt, var,
        b, u, i, center,
        dl, dt, dd, ol, ul, li,
        fieldset, form, label, legend,
        table, caption, tbody, tfoot, thead, tr, th, td,
        article, aside, canvas, details, embed,
        figure, figcaption, footer, header, hgroup,
        menu, nav, output, ruby, section, summary,
        time, mark, audio, video {
            margin: 0;
            padding: 0;
            border: 0;
            font-size: 100%;
            font: inherit;
            vertical-align: baseline; }

        @font-face {
            font-family: 'Agency FB';
            src: url('/media/fonts/AgencyFB-Bold.eot');
            src: url('/media/fonts/AgencyFB-Bold.eot?#iefix') format('embedded-opentype'),
            url('/media/fonts/AgencyFB-Bold.woff2') format('woff2'),
            url('/media/fonts/AgencyFB-Bold.woff') format('woff'),
            url('/media/fonts/AgencyFB-Bold.ttf') format('truetype');
            font-weight: bold;
            font-style: normal;
        }
        @font-face {
            font-family: 'Agency FB';
            src: url('/media/fonts/AgencyFB-Reg.eot');
            src: url('/media/fonts/AgencyFB-Reg.eot?#iefix') format('embedded-opentype'),
            url('/media/fonts/AgencyFB-Reg.woff2') format('woff2'),
            url('/media/fonts/AgencyFB-Reg.woff') format('woff'),
            url('/media/fonts/AgencyFB-Reg.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
            font-family: 'SEGOEUIL';
            src: url('/media/fonts/SEGOEUIL.eot');
            src: local('SEGOEUIL'),
            url('/media/fonts/SEGOEUIL.woff') format('woff'),
            url('/media/fonts/SEGOEUIL.ttf') format('truetype');
        }

        html, body {
            width: 100%;
            height: 100%;
        }
        .error-page {
            background: url("/media/img/login_bg.jpg") no-repeat;
            background-size: 100% 100%;
            position: relative;
        }
        .error-page .error-message {
            width: 400px;
            height: 670px;
            margin: 30px auto;
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
        }
        .error-page .error-logo {
            text-align: center;
            margin-bottom: 150px;
        }
        .error-page .error-logo img{
            display: inline-block;
            vertical-align: middle;
            width: 93px;
            height: 109px;
            margin-left: 12px;
        }
        .rtl .error-page .error-logo img{
            display: inline-block;
            vertical-align: middle;
            margin-left: 0;
            margin-right: 10px;
        }
        .error-page .error-sign {
            text-align: center;
            margin-bottom: 25px;
        }
        .error-page .error-sign img{
            display: inline-block;
            vertical-align: middle;
            width: 57px;
            height: 67px;
        }
        .error-page .error-code {
            margin: 0 auto 15px;
            color: #1ebae5;
            text-align: center;
            font-size: 198px;
            font-weight: normal;
            font-style: normal;
            font-family: "Agency FB", "proxima_nova_rgregular", Arial, Helvetica, sans-serif;
            line-height: 165px;
        }
        .error-page .error-code-desc {
            text-align: center;
            color: #708492;
            font-size: 40px;
            font-weight: lighter;
            font-style: italic;
            font-family: "SEGOEUIL", "proxima_nova_rgregular", Arial, Helvetica, sans-serif;
            margin-bottom: 3px;
            line-height: 32px;
        }
        .error-page .error-code-desc-small {
            text-align: center;
            color: #708492;
            font-size: 25px;
            font-weight: lighter;
            font-style: italic;
            font-family: "SEGOEUIL", "proxima_nova_rgregular", Arial, Helvetica, sans-serif;
            margin-bottom: 20px;
            line-height: 32px;
        }
        .error-page .go-to-home-page {
            display: block;
            height: 40px;
            line-height: 40px;
            width: 235px;
            margin: 0 auto;
            background: #f2faff;
            color: #708492;
            padding: 2px 12px;
            text-align: center;
            font-size: 18px;
            font-weight: normal;
            font-style: normal;
            font-family: "proxima_nova_rgregular", Arial, Helvetica, sans-serif;
            cursor: pointer;
            border: 1px solid #1ebae5;
            -webkit-border-radius: 6px;
            -moz-border-radius: 6px;
            -ms-border-radius: 6px;
            border-radius: 6px;
            text-decoration: none;
        }
        .error-page .go-to-home-page:hover{
            background: #1ebae5;
            color: white;
            text-decoration: none;
        }

        @media (max-width: 640px) {
            .error-page .error-message {
                width: 100%;
                height: 350px;
                margin: 15px auto;
                position: absolute;
                top: 0;
                left: 0;
                bottom: 0;
                right: 0;
            }
            .error-page .error-logo img{
                display: inline-block;
                vertical-align: middle;
                width: 70px;
                height: 80px;
            }
            .error-page .error-sign{
                margin-bottom: 15px;
            }
            .error-page .error-code {
                font-size: 75px;
                margin: 0 auto 10px;
                line-height: 70px;
            }
            .error-page .error-code-desc {
                font-size: 24px;
                line-height: 18px;
            }
            .error-page .error-code-desc-small {
                font-size: 16px;
                line-height: 15px;
                margin-bottom: 5px;
            }
            .error-page .go-to-home-page {
                height: 30px;
                line-height: 30px;
                width: 210px;
                font-size: 16px;
            }
        }
        @media (max-width: 640px) and (orientation: portrait){
            .error-page .error-logo {
                margin-bottom: 45px;
            }
        }
        @media (max-width: 640px) and (orientation: landscape){
            .error-page .error-message {
                margin: 10px auto 0;
            }
            .error-page .error-logo {
                margin-bottom: 15px;
            }
        }
        @media (max-width: 480px) and (orientation: portrait){
            .error-page .error-logo {
                margin-bottom: 45px;
            }
        }
        @media (max-width: 480px) and (orientation: landscape){
            .error-page .error-message {
                height: 310px;
                margin: 5px auto 0;
            }

            .error-page .error-logo {
                margin-bottom: 10px;
            }
            .error-page .error-code {
                font-size: 60px;
                margin: 0 auto 5px;
                line-height: 50px;
            }
            .error-page .error-code-desc {
                font-size: 18px;
                margin-bottom: 5px;
            }
        }
    </style>
    </head>
<body class="error-page">
<div class="error-message">
    <div class="error-logo">
        <img src="/media/img/error-logo.png" alt="logo">
    </div>
    <div class="error-sign">
        <img src="/media/img/error-sign-permission.png" alt="error sign">
    </div>
    <div class="error-code">403</div>
    <div class="error-code-desc"><?=__('Access Denied')?> </div>
    <div class="error-code-desc-small"><?=__('you don’t have permissions')?></div>
    <a href="<?=URL::site('/')?>" class="go-to-home-page"><?=__('Go to homepage')?></a>

</div>
</body>
</html>
<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 12.05.2020
 * Time: 6:33
 */

$ii = 0;

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
                <img src="/media/img/new-images/quality.png" alt="" class="quality-mark">
                <img src="/media/img/new-images/q4b-logo.png" alt="" class="q4b-logo">
            </div>
            <div class="pdf-info">
                <div class="project-logo">
                    <img src="/media/img/new-images/quality.png" alt="xzxzz">
                </div>

                <div class="pdf-lists">
                    <div class="pdf-list-top">
                        <ul class="pdf-ul">
                            <li>פרוטוקול מסירה מס :</li>
                            <li>שם עובד :</li>
                        </ul>



                        <ul class="pdf-ul">
                            <li>תאריך :</li>
                        </ul>

                    </div>
                    <div class="pdf-list-bottom">

                        <ul class="pdf-ul">
                            <li>שם הפרויקט :</li>
                            <li>מבנה :</li>
                            <li>קומה :</li>
                        </ul>
                        <ul class="pdf-ul">
                            <li>מרחב :</li>
                            <li>מחסן/'ם :</li>
                            <li>חניה/ות :</li>
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
                <div class="pr-dt">
                    <div class="pr-dt-info">
                        <ul class="pdf-ul">
                            <li>inchvor mi ban</span></li>
                        </ul>
                    </div>
                    <div class="img">
                        <img src="/media/data/customers/121211221.jpg" alt="">
                    </div>
                </div>
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

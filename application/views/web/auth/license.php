<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 17.01.2017
 * Time: 13:28
 */
?>

<div id="licence-agreement-modal" data-backdrop="static" data-keyboard="false"  class="modal fade" role="dialog">
    <div class="modal-dialog q4_project_modal licence-agreement-modal-dialog">
        <div class="modal-content">
            <form class="q4_form" action="<?=URL::site('user/agree_terms')?>" method="post" data-ajax="true">
                <input type="hidden" value="" name="x-form-secure-tkn"/>
                <div class="modal-header q4_modal_header">
                    <div class="q4_modal_header-top">
                        <div class="clear"></div>
                    </div>
                </div>
                <div class="modal-body">

                    <div class="licence-agreement-logo">
                        <a href="#" title="logo"><img src="/media/img/license-logo.png" alt="logo"/></a>
                    </div>

                    <div class="wrap-licence-agreement-title">
                        <h2><?=__('Q4B – QUALITY MADE SIMPLE')?></h2>
                        <h2><?=__('END USER LICENSE AGREEMENT')?></h2>
                    </div>
                    <div class="wrap-licence-agreement">
                        <article><?=__('PLEASE READ THE')?> <span class="light-blue"><?=__('TERMS AND CONDITIONS')?></span><?=__('OF THIS END USER')?> </article>
                        <article><?=__('THIS EULA IS A LEGALLY')?></article>
                        <article><?=__('BY PERFORMING ANY OF THE')?></article>
                        <article><?=__('IF YOU ARE INSTALLING')?></article>

                        <article class="orange"><?=__('IF YOU DO NOT AGREE')?></article>

                        <p><span class="num">1 </span> <?=__('Software License Grant')?></p>

                        <p><span class="num">2 </span><?=__('Ancillary equipment')?></p>

                        <p><span class="num">3 </span><?=__('Open Source Code')?></p>

                        <p><span class="num">4 </span><?=__('Limitations on License')?></p>

                        <p><span class="num">5 </span><?=__('Responsibility for Data incorporated')?></p>

                        <p><span class="num">6 </span><?=__('Compliance with Laws')?></p>

                        <p><span class="num">7 </span><?=__("Maintenance and Support Services")?></p>

                        <p><span class="num">8 </span><?=__('Licensee Default')?></p>

                        <p><span class="num">9 </span><?=__('The Fee. The price for the License')?></p>

                        <p><span class="num">10 </span><?=__('Term. This EULA is effective')?></p>

                        <p><span class="num">11 </span><?=__('Intellectual Property')?></p>

                        <p><?=__('It is clarified that any names')?></p>

                        <p><span class="num">12 </span><?=__('Feedbacks. If You send the Licensor')?></p>

                        <p><span class="num">13 </span><?=__('Confidentiality Security')?></p>

                        <p><span class="num">14 </span><?=__('DISCLAIMER OF WARRANTY')?></p>

                        <p><?=__('WITHOUT DEROGATING FROM THE FOREGOING')?></p>

                        <p><span class="num">15 </span><?=__('LIMITATION OF LIABILITY')?></p>

                        <p><span class="num">16 </span><?=__('Indemnification. You agree to indemnify')?></p>

                        <p><span class="num">17 </span><?=__('Upgrades, etc. Upgrades')?></p>

                        <p><span class="num">18 </span><?=__('Safe Performance. The Software is not designed')?></p>

                        <p><span class="num">19 </span><?=__('Audit. The Licensor may')?></p>

                        <p><span class="num">20 </span><?=__('Governing Law and Jurisdiction')?></p>

                        <p><span class="num">21 </span><?=__('Severability. Should any term')?></p>

                        <p><span class="num">22 </span><?=__('No Waiver. The failure of either')?></p>

                        <p><span class="num">23 </span><?=__('Reservation of Rights. All rights')?></p>

                        <p><span class="num">24 </span><?=__('Assignment. Any attempt by Licensee')?></p>

                        <p><span class="num">25 </span><?=__('Interpretation. In the event of any conflict')?></p>
                    </div>
                </div>
                <div class="panel-modal-footer text-right-left">
                    <div class="row">
                        <div class="col-md-12">
                            <span class="q4-btn-lg light-blue-bg submit agree-with-terms"><?=__('Confirm')?></span>
                        </div>
                    </div>
                </div>
            </form>
         </div>
    </div>
 </div>


<? defined('SYSPATH') OR die('No direct script access.'); ?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 03.12.2016
 * Time: 6:14
 */

?>

<section class="content-projects">
    <div class="mobile-layout">
        <!--mobile filter-->


        <div class="q4-wrap-mobile">
            <div data-structurecount="2" class="q4-list-items-mobile q4-owl-carousel">
                <div class="item">
                    <figure class="mobile-figure">
                        <a href="<?=URL::site('reports')?>">
                            <img src="/media/img/reports_icon.svg" alt="<?=__('QC Report')?>">
                        </a>
                        <figcaption class="mobile-fig-caption">
                            <a href="<?=URL::site('reports')?>"><?=__('QC Report')?></a>
                        </figcaption>
                    </figure>
                </div>
<!--                отключаю на мобильном-->
                <?if(false and Usr::can(Usr::READ_PERM,'Controller_QualityReports',Enum_UserPriorityLevel::General)):?>
                    <div class="item">
                        <figure class="mobile-figure">
                            <a href="<?=URL::site('reports/quality')?>">
                                <img src="/media/img/qreport.svg" alt="<?=__('Quality Report')?>" style="height: 71%;margin-left: 14px;">
                            </a>
                            <figcaption class="mobile-fig-caption">
                                <a href="<?=URL::site('reports/quality')?>"><?=__('Quality Report')?></a>
                            </figcaption>
                        </figure>
                    </div>
                <?endif;?>
<!--                --><?//if(Usr::can(Usr::READ_PERM,'Controller_TasksReports',Enum_UserPriorityLevel::General)):?>
                    <div class="item">
                        <figure class="mobile-figure">
                            <a href="<?=URL::site('reports/tasks')?>">
                                <img src="/media/img/treport.svg" alt="<?=__('Tasks report')?>" style="height: 71%;margin-left: 14px;">
                            </a>
                            <figcaption class="mobile-fig-caption">
                                <a href="<?=URL::site('reports/tasks')?>"><?=__('Tasks report')?></a>
                            </figcaption>
                        </figure>
                    </div>
<!--                --><?//endif;?>

<!--                --><?//if(Usr::can(Usr::READ_PERM,'Controller_TasksReports',Enum_UserPriorityLevel::General)):?>
                    <div class="item">
                        <figure class="mobile-figure">
                            <a href="<?=URL::site('reports/place')?>">
                                <img src="/media/img/preport.svg" alt="<?=__('Place report')?>" style="height: 71%;margin-left: 14px;">
                            </a>
                            <figcaption class="mobile-fig-caption">
                                <a href="<?=URL::site('reports/place')?>"><?=__('Place report')?></a>
                            </figcaption>
                        </figure>
                    </div>
<!--                --><?//endif;?>
                <?if(Usr::can(Usr::READ_PERM,'Controller_DeliveryReports',Enum_UserPriorityLevel::General) OR in_array(strtolower(Auth::instance()->get_user()->email),Kohana::$config->load('deliveryPermissionsEmails')->as_array())):?>
                <div class="item">
                    <figure class="mobile-figure">
                        <a href="<?=URL::site('reports/delivery')?>">
                            <img src="/media/img/dlreport.svg" alt="<?=__('Delivery report')?>" style="height: 71%;margin-left: 14px;">
                        </a>
                        <figcaption class="mobile-fig-caption">
                            <a href="<?=URL::site('reports/delivery')?>"><?=__('Delivery report')?></a>
                        </figcaption>
                    </figure>
                </div>
                <?endif;?>
                <div class="item">
                    <figure class="mobile-figure">
                        <a href="<?=URL::site('reports/labtests')?>">
                            <img src="/media/img/lab-control.svg" alt="<?=__('lab_control_reports')?>" style="width: 75% !important;">
                        </a>
                        <figcaption class="mobile-fig-caption">
                            <a href="<?=URL::site('reports/labtests')?>"><?=__('lab_control_reports')?></a>
                        </figcaption>
                    </figure>
                </div>
                <div class="item">
                    <figure class="mobile-figure">
                        <a href="<?=URL::site('reports/approve_element')?>">
                            <img src="/media/img/approve_element_report_icon.svg" alt="<?=__('approve_element_uppercase')?>" style="height: 71%;margin-left: 14px;">
                        </a>
                        <figcaption class="mobile-fig-caption">
                            <a href="<?=URL::site('reports/approve_element')?>"><?=__('approve_element_uppercase')?></a>
                        </figcaption>
                    </figure>
                </div>
                <?if(Usr::can(Usr::READ_PERM,'Controller_CertificatesReports')):?>
                <div class="item">
                    <figure class="mobile-figure">
                        <a href="<?=URL::site('reports/certificates')?>">
                            <img src="/media/img/certificate.svg" alt="<?=__('certificates_reports')?>" style="width: 75% !important;margin-top: 7px;">
                        </a>
                        <figcaption class="mobile-fig-caption">
                            <a href="<?=URL::site('reports/certificates')?>"><?=__('certificates_reports')?></a>
                        </figcaption>
                    </figure>
                </div>
                <?endif;?>
            </div>

        </div>
    </div>

</section>

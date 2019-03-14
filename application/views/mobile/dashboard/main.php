<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 31.05.2017
 * Time: 14:42
 */
?>
<div id="dashboard-content">

    <section class="content-dashboard">
 <!----Start ---->
        <div class="mobile_item--link-wrapper">
            <a href="#" class="mobile_item--link">
                <i class="mobile_item--link-icon q4b-mobile-manager_console"></i>
                <span class="mobile_item--link-title">Manager Console</span>
            </a>
            <a href="#" class="mobile_item--link">
                <i class="mobile_item--link-icon q4b-mobile-plan"></i>
                <span class="mobile_item--link-title">Plans</span>
            </a>
            <a href="#" class="mobile_item--link">
                <i class="mobile_item--link-icon q4b-mobile-Reports"></i>
                <span class="mobile_item--link-title">Reports</span>
            </a>
            <a href="#" class="mobile_item--link">
                <i class="mobile_item--link-icon q4b-mobile-consultation"></i>
                <span class="mobile_item--link-title">Consulting and Auditors</span>
            </a>
        </div>

        <a href="#" class="mobile_item-create-qc">
            <i class="q4b-mobile-create_qc"></i>
            <span>Create Quality Control</span>
        </a>
<!----End ---->
        <div class="dash-item-icons">
        	<?=$filterView?>
        </div>
        <ul>
            <li data-tab="quality-control-tab" class="tab_panel quality-control-tab">
                <?=$qualityControlsView?>
            </li>

            <li data-tab="certification-tab" class="tab_panel certification-tab">
                <?=$certificationsView?>
            </li>
        </ul>
    </section>

</div>

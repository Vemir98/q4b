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

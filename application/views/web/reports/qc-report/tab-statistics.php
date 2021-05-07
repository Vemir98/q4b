<?php
/**
 * Created by PhpStorm.
 * User: lusine
 * Date: 4/23/21
 * Time: 4:59 PM
 */
?>
<div class="tab_content report-status-list report-statistic-redesign double-result" id="tab_statistics" style="display: none">
    <?if(empty($del_rep_id)):?>
        <?=View::make($_VIEWPATH.'statistics',
            [
                'crafts' => $crafts,
                'craftsParams' => $craftsParams,
                'filteredCraftsParams' => $filteredCraftsParams,
                'craftsList' => $craftsList,
                'filteredCraftsList' => $filteredCraftsList,
                'craftName' => $qcs[0]->craft->name
            ])?>

    <?endif;?>
</div>










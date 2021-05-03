<?php
/**
 * Created by PhpStorm.
 * User: lusine
 * Date: 4/23/21
 * Time: 5:07 PM
 */
?>

<div class="report-project-desc tab_content report-result" id="tab_qc_controls" style="display: none">
    <?foreach ($qcs as $q): ?>
        <?=View::make($_VIEWPATH.'list-item',
            [
                'q' => $q,
                'tasks' => $tasks
            ])?>
    <?endforeach?>
    <?=$pagination?>
</div>

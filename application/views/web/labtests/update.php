<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: lusine
 * Date: 5/15/21
 * Time: 3:41 PM
 */
$ltStatuses = array_values(Enum_LabtestStatus::toArray());
$role = $_USER->getRelevantRole('name');
?>
<div id="pr-labtests-list" class="new-styles">
    <labtest-update
            site-url="<?=trim(URL::site('','https'),'/')?>"
            project-id="<?=$projectId?>"
            labtest-id="<?=$labtestId?>"
            translations='<?=json_encode($translations)?>'
            statuses='<?=json_encode($ltStatuses)?>'
            role="<?=$role?>"
    />
</div>
<script>
    var app = new Vue({
        el: '#pr-labtests-list',
    })
</script>

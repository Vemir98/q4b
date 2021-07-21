<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: lusine
 * Date: 5/15/21
 * Time: 3:41 PM
 */
$ltStatuses = array_values(Enum_LabtestStatus::toArray());
?>
<div id="pr-labtests-list" class="new-styles">
    <pr-labtests-list
            site-url="<?=trim(URL::site('','https'),'/')?>"
            project-id="<?=$projectId?>"
            translations='<?=json_encode($translations)?>'
            statuses='<?=json_encode($ltStatuses)?>'
    />
</div>
<script>
    Vue.component('pagination', Pagination);
    var app = new Vue({
        el: '#pr-labtests-list',
    })
</script>

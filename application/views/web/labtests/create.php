<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: Vemir
 * Date: 10/01/22
 * Time: 12:06 PM
 */
?>
<div id="pr-labtests-create" class="new-styles">
    <pr-labtests-create
            site-url="<?=trim(URL::site('','https'),'/')?>"
            project-id="<?=$projectId?>"
            translations='<?=json_encode($translations)?>'
            statuses='<?=json_encode(Enum_Status::toArray())?>'
    />
</div>
<script>
    var app = new Vue({
        el: '#pr-labtests-create',
    })
</script>

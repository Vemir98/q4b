<?defined('SYSPATH') OR die('No direct script access.');?>

<?php
/**
 * Created by PhpStorm.
 * User: lusine
 * Date: 5/15/21
 * Time: 3:41 PM
 */
?>
<div id="elements-list">
    <elements-list
            project-id="<?=$projectId?>"
            translations='<?=json_encode($translations)?>'
    ></elements-list>
</div>
<script>
    var app = new Vue({
        el: '#elements-list',
    })
</script>

<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: Vemir
 * Date: 17.11.2021
 * Time: 11:20
 */
?>
<div id="info-center">
    <info-center-page
        site-url="<?=trim(URL::site('','https'),'/')?>"
        translations='<?=json_encode($translations)?>'
    />
</div>
<script>
    var app = new Vue({
        el: '#info-center',
    })
</script>
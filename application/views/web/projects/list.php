<?defined('SYSPATH') OR die('No direct script access.');?>

<?php
/**
 * Created by PhpStorm.
 * User: lusine
 * Date: 5/15/21
 * Time: 3:41 PM
 */
?>
<div id="labtests-projects" class="new-styles">
    <projects-list
            site-url="<?=trim(URL::site('','http'),'/')?>/"
            translations='<?=json_encode($translations)?>'
            redirect-url="projects/update/"
    />
</div>
<script>
    Vue.component('pagination', Pagination);
    var app = new Vue({
        el: '#labtests-projects',
    })
</script>

<?defined('SYSPATH') OR die('No direct script access.');?>

<div id="tasks-list">
    <tasks-list
            project-id="<?=$projectId?>"
            translations='<?=json_encode($translations)?>'
    ></tasks-list>
</div>
<script>
    var app = new Vue({
        el: '#tasks-list',
    })
</script>

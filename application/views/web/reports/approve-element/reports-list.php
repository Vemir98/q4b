<?defined('SYSPATH') OR die('No direct script access.');?>

<div id="approve-element-reports-list">
    <reports-list
        :data='<?=json_encode($data)?>'
        translations='<?=json_encode($translations)?>'
    />
</div>


<script>
    var app = new Vue({
        el: '#approve-element-reports-list',
    })
</script>
<?defined('SYSPATH') OR die('No direct script access.');?>
<?php

$ltStatuses = array_values(Enum_LabtestStatus::toArray());
$generateReportsUrl = URL::site('reports/approve_element/generate');


?>
<div id="approve-element">
    <generate-reports
        :statuses='<?=json_encode($ltStatuses)?>'
        translations='<?=json_encode($translations)?>'
    />
</div>
<script>
    var app = new Vue({
        el: '#approve-element',
    })
</script>

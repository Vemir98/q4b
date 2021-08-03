<?defined('SYSPATH') OR die('No direct script access.');?>

<div id="approve-element">
    <approve-elements-tab
        :data='<?=json_encode($data)?>'
        translations='<?=json_encode($translations)?>'
        :statuses='<?=json_encode(array_values(Enum_LabtestStatus::toArray()))?>'
    />
</div>
<script>
    var app = new Vue({
        el: '#approve-element',
    })
</script>

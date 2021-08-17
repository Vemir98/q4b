<?defined('SYSPATH') OR die('No direct script access.');?>

<div id="approve-element">
    <approve-elements-tab
        :username='<?=json_encode(Auth::instance()->get_user()->name)?>'
        translations='<?=json_encode($translations)?>'
        :statuses='<?=json_encode(array_values(Enum_ElementApprovalReportsStatus::toArray()))?>'
    />
</div>
<script>
    var app = new Vue({
        el: '#approve-element',
    })
</script>

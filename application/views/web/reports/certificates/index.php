<?defined('SYSPATH') OR die('No direct script access.');?>

<div id="certificates-reports">
    <certificates-reports
        translations='<?=json_encode($translations)?>'
        statuses='<?=json_encode(Enum_ApprovalStatus::toArray())?>'
        site-url='<?=trim(URL::site('','https'),'/')?>'
        image-url='<?=URL::withLang('/','en')?>'
    />
</div>
<script>
    Vue.component('pagination', Pagination);
    var app = new Vue({
        el: '#certificates-reports',
    })
</script>

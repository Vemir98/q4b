<?defined('SYSPATH') OR die('No direct script access.');?>

<div id="element-approval-create" class="new-styles">
    <element-approval-create
        user-role='<?=$userRole?>'
        site-url='<?=trim(URL::site('','https'),'/')?>'
        image-url='<?=URL::withLang('/','en')?>'
        user-profession="<?=$userProfession?>"
        :username='<?=json_encode(Auth::instance()->get_user()->name)?>'
        translations='<?=json_encode($translations)?>'
        statuses='<?=json_encode(Enum_Status::toArray())?>'
        project-id="<?=$projectId?>"
    />
</div>
<script>
    Vue.component('pagination', Pagination);
    var app = new Vue({
        el: '#element-approval-create',
    })
</script>

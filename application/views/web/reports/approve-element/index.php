<?defined('SYSPATH') OR die('No direct script access.');?>

<div id="approve-element">
    <approve-elements-tab
        user-role='<?=$userRole?>'
        site-url='<?=trim(URL::site('','https'),'/')?>'
        image-url='<?=URL::withLang('/','en')?>'
        user-profession="<?=$userProfession?>"
        :username='<?=json_encode(Auth::instance()->get_user()->name)?>'
        translations='<?=json_encode($translations)?>'
        :statuses='<?=json_encode(array_values(Enum_ApprovalStatus::toArray()))?>'
        project-id="<?=$projectId?>"
    />
</div>
<script>
    Vue.component('pagination', Pagination);
    var app = new Vue({
        el: '#approve-element',
    })
</script>

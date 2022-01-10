<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 31.05.2017
 * Time: 14:42
 */
//die('mtav');
?>

<!--project-ids="--><?//=json_encode($projectIds)?><!--"-->
<!--range="--><?//=json_encode($range)?><!--"-->
<div id="dashboard">
    <statistics-pr
            translations='<?=json_encode($translations)?>'
            site-url="<?=trim(URL::site('','https'),'/')?>"
            user-preferences-types='<?=json_encode($userPreferencesTypes)?>'
            user-id='<?=Auth::instance()->get_user()->id?>'
    />
</div>
<script>
    var app = new Vue({
        el: '#dashboard',
    })
</script>
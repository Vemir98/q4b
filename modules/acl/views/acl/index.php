<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 07.09.2016
 * Time: 6:27
 */
?>
<div class="index">
    <div class="actions">
        <a href="<?=Route::url('acl.resources-manager',['action' => 'add_res'])?>">Add New Resource</a> <br>
        <a href="<?=Route::url('acl.resources-manager',['action' => 'add_priv'])?>">Add New Privilege</a> <br>
    </div>
    <div class="resources">
        <h3>Resources list</h3>
        <?if($resources->count()):?>
        <ul>
            <?foreach ($resources as $r):?>
                <li>
                    <?=$r->name,' (',$r->privileges->count_all(),')'?>
                    | <a href="<?=Route::url('acl.resources-manager',['action' => 'edit_res', 'id' => $r->id])?>">Edit</a>
                    | <a href="<?=Route::url('acl.resources-manager',['action' => 'remove_res', 'id' => $r->id])?>" class="remove">Remove</a>
                </li>
            <?endforeach;?>
        </ul>
        <?else:?>
        There are nothing to show
        <?endif?>
    </div>
    <div class="privileges">
        <h3>Privileges list</h3>
        <?if($privileges->count()):?>
            <ul>
                <?foreach ($privileges as $p):?>
                    <li>
                        <?=$p->name,' (',$p->resources->count_all(),')'?>
                        | <a href="<?=Route::url('acl.resources-manager',['action' => 'edit_priv', 'id' => $p->id])?>">Edit</a>
                        | <a href="<?=Route::url('acl.resources-manager',['action' => 'remove_priv', 'id' => $p->id])?>" class="remove">Remove</a>
                    </li>
                <?endforeach;?>
            </ul>
        <?else:?>
            There are nothing to show
        <?endif?>
    </div>
    <div class="roles">
        <h3>Manage Roles Privileges</h3>
        <?if($roles->count()):?>
            <ul>
                <?foreach ($roles as $r):?>
                <li>
                    <?=$r->name?>
                    | <a href="<?=Route::url('acl.resources-manager',['action' => 'mng_role_priv', 'id' => $r->id])?>">Edit</a>
                    | <a href="<?=Route::url('acl.resources-manager',['action' => 'remove_priv', 'id' => $r->id])?>" class="remove">Remove</a>
                </li>
                <?endforeach;?>
            </ul>
        <?endif?>
    </div>
</div>
<script>
    (function() {
        var items = document.getElementsByClassName('remove');
        for(var i = 0; i < items.length; i++){
            items[i].addEventListener('click', function(e) {
                if( ! confirm('Are You want to delete this item??? If You delete this item,\nall relations with this item will be deleted.')){
                    e.preventDefault();
                }
            });

        }

    })();
</script>
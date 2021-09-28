<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: Vemir
 * Date: 25.09.2021
 * Time: 17:55
 */
?>

<section class="content_new_projects">
    <div class="tab_panel">
        <div class="panel_header">
            <h2><?=__('Users')?></h2>
        </div>
        <div class="panel_content">
            <?=View::make($_VIEWPATH.'../users/form',
                ['action' => URL::site('projects/remove_users/'.$_PROJECT->id),
                    'items' => $users,
                    'secure_tkn' => AesCtr::encrypt($_PROJECT->id.Text::random('alpha'),$_PROJECT->id,192)
                ])?>
        </div>
    </div>
</section>
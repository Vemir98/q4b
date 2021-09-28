<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: Vemir
 * Date: 25.09.2021
 * Time: 14:46
 */
?>
<section class="content_new_company">
    <div class="tab_panel ">
        <div class="panel_header">
            <h2><?=__('Crafts')?></h2>
        </div>
        <div class="panel_content">
            <?=View::make($_VIEWPATH.'../crafts/form',
                ['action' => URL::site('companies/update_crafts/'.$company->id),
                    'items' => $crafts,
                    'secure_tkn' => AesCtr::encrypt($company->id.Text::random('alpha'),$company->id,192)
                ])?>
        </div>
    </div>
</section>

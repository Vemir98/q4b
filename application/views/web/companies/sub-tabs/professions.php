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
    <ul>
        <li class="tab-panel">
            <div class="panel_header open">
                <h2><?=__('Professions')?></h2>
            </div>
            <div class="panel_content open">
                <?=View::make($_VIEWPATH.'../professions/form',
                    ['action' => URL::site('companies/update_professions/'.$company->id),
                        'items' => $professions,
                        'items_crafts' => $professionsSelectedCrafts,
                        'crafts' => $crafts,
                        'secure_tkn' => AesCtr::encrypt($company->id.Text::random('alpha'),$company->id,192)
                    ])?>
            </div>
        </li>
    </ul>
</section>

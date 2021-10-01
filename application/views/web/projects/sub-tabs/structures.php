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
    <ul>
        <li class="tab-panel">
            <div class="panel_header open">
                <h2><?=__('Property Group')?></h2>
            </div>
            <div class="panel_content property-tab-content open">
                <?=View::make($_VIEWPATH.'../property/form',
                    ['action' => URL::site('projects/update_properties/'.$_PROJECT->id),
                        'items' => $_PROJECT->objects->order_by('id','DESC')->find_all(),
                        'itemTypes' => ORM::factory('PrObjectType')->find_all(),
                        'secure_tkn' => AesCtr::encrypt($_PROJECT->id.Text::random('alpha'),$_PROJECT->id,192)
                    ])?>
            </div>
        </li>
    </ul>
</section>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 17.07.2017
 * Time: 11:32
 */
?>
<li class="tab_panel">
    <div class="panel_header">
        <span class="sign"><i class="panel_header_icon q4bikon-plus"></i></span><h2><?=__('Users')?></h2>
    </div>
    <div class="panel_content">
        <?=View::make($_VIEWPATH.'users/form',
            ['action' => URL::site('companies/update_users/'.$company->id),
                'items' => $users,
                'professions' => $professions,
                'roles' => $userRoles,
                'secure_tkn' => AesCtr::encrypt($company->id.Text::random('alpha'),$company->id,192)
            ])?>
    </div>

</li>
<li class="tab_panel">
    <div class="panel_header">
        <span class="sign"><i class="panel_header_icon q4bikon-plus"></i></span><h2><?=__('Professions')?></h2>
    </div>
    <div class="panel_content">
        <?=View::make($_VIEWPATH.'professions/form',
            ['action' => URL::site('companies/update_professions/'.$company->id),
                'items' => $professions,
                'items_crafts' => $professionsSelectedCrafts,
                'crafts' => $crafts,
                'secure_tkn' => AesCtr::encrypt($company->id.Text::random('alpha'),$company->id,192)
            ])?>
    </div><!--.panel_content-->
</li>
<li class="tab_panel">
    <div class="panel_header">
        <span class="sign"><i class="panel_header_icon q4bikon-plus"></i></span><h2><?=__('Standards')?></h2>
    </div>
    <div class="panel_content">
        <?=View::make($_VIEWPATH.'standards/form',
            ['action' => URL::site('companies/update_standards/'.$company->id),
                'items' => $standards,
                'users' => $users,
                'secure_tkn' => AesCtr::encrypt($company->id.Text::random('alpha'),$company->id,192)
            ])?>
    </div><!--.panel_content-->
</li>
<li class="tab_panel">
    <div class="panel_header">
        <span class="sign"><i class="panel_header_icon q4bikon-plus"></i></span><h2><?=__('Links to other systems')?></h2>
    </div>
    <div class="panel_content">
        <?=View::make($_VIEWPATH.'links/form',
            ['action' => URL::site('companies/update_links/'.$company->id),
                'items' => $company->links->order_by('id','DESC')->find_all(),
                'secure_tkn' => AesCtr::encrypt($company->id.Text::random('alpha'),$company->id,192)
            ])?>
    </div><!--.panel_content -->
</li>

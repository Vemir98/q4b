<?php defined('SYSPATH') OR die('No direct access allowed.');?>
<? if (count($breadcrumbs) > 0) : ?>
<div class="breadcrumbs">
<span></span>
<? foreach ($breadcrumbs as $crumb) : ?>
<? if (!empty($crumb->get_url())) : ?>
&gt;<a href="<?=URL::site($crumb->get_url())?>"><?=$crumb->get_title()?></a>
<? else : ?>
<strong><?=$crumb->get_title()?></strong>
<? endif; ?>
<? endforeach; ?>
</div>
<? endif; ?>

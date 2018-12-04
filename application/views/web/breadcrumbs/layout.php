
<?php defined('SYSPATH') OR die('No direct access allowed.');?>
<?if(count($breadcrumbs)>0):?>
	<ul class="breadcrumbs">
	    <span></span>
	    <?foreach ($breadcrumbs as $i=>$crumb):?>
	        <?if (!empty($crumb->get_url())):?>
	            <li>
	    			<?if($i>0):?>
		            	<i class="q4bikon-double_arrow_bottom"></i>
		            <?endif;?>
		            <a href="<?=URL::site($crumb->get_url())?>">
		            	<?=__($crumb->get_title())?>
		            </a>
	            </li>
	        <?else:?>
	            <li>
	            	<i class="q4bikon-double_arrow_bottom"></i>
	            	<span class="breadcrumb-text">
	            		<?=__($crumb->get_title())?>
	            	</span>
	            </li>
	        <?endif;?>
	    <?endforeach;?>
	</ul>
<? endif; ?>

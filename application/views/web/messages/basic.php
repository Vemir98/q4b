<?php defined('SYSPATH') or die('No direct script access.');?>
<?php if (isset($messages) AND !empty($messages)): ?>
	<?php foreach ($messages as $message): ?>
		<div class="alert alert-<?php echo $message->type ?>">
			<button type="button" class="close" data-dismiss="alert"><i class="q4bikon-remove-circle"></i></button>
			<?php echo $message->text ?>
		</div>
	<?php endforeach; ?>
<?php endif;?>
<?php
$entity = elgg_extract('entity', $vars);

if (!$entity instanceof hypeJunction\Images\Avatar) {
	return;
}
?>
<div class="elgg-text-help">
	<?php echo elgg_echo('avatars:crop:instructions') ?>
</div>
<div>
	<?php
	echo elgg_view('input/cropper', [
		'src' => $entity->getDownloadUrl(),
		'name' => 'crop_coords',
		'x1' => $entity->x1,
		'y1' => $entity->y1,
		'x2' => $entity->x2,
		'y2' => $entity->y2,
	]);
	?>
</div>
<div class="elgg-foot">
	<?php
	echo elgg_view('input/hidden', ['name' => 'guid', 'value' => $entity->guid]);
	echo elgg_view('input/submit', ['value' => elgg_echo('avatars:crop')]);
	?>
</div>

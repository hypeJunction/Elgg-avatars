<?php

/**
 * @uses $vars['entity'] Entity to which the avatar belongs
 */

$entity = elgg_extract('entity', $vars);
$value = false;
$image = '';

if (!isset($vars['name'])) {
	$vars['name'] = 'avatar';
}

$vars['accept'] = "image/*";

if ($entity instanceof ElggEntity) {
	$avatar = avatars_get_avatar($entity);
	if ($avatar) {
		$value = true;
		$image = elgg_view_entity_icon($avatar, 'small');
	}
}

$body = elgg_view('input/file', $vars);

echo elgg_view_image_block('', $body, [
	'image_alt' => $image,
]);
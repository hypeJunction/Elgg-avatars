<?php

$params = new stdClass();

$input_keys = array_keys((array) elgg_get_config('input'));
$request_keys = array_keys((array) $_REQUEST);
$keys = array_unique(array_merge($input_keys, $request_keys));
foreach ($keys as $key) {
	if ($key) {
		$params->$key = get_input($key);
	}
}

$entity = get_entity($params->guid);
if (!$entity instanceof hypeJunction\Images\Avatar) {
	register_error(elgg_echo('avatars:error:not_found'));
	forward(REFERRER);
}

if (!$entity->canEdit()) {
	register_error(elgg_echo('avatars:error:permission_denied'));
	forward(REFERRER);
}

foreach (array('x1', 'y1', 'x2', 'y2') as $coord) {
	$value = elgg_extract($coord, $params->crop_coords, 0);
	$entity->$coord = (int) round($value, 0);
}

// Updade image's modified time in order to regenerate thumbs
touch($entity->getFilenameOnFilestore());

if ($entity->save()) {
	$container = $entity->getContainerEntity();
	if ($container instanceof ElggUser) {
		if (elgg_trigger_event('profileiconupdate', $container->type, $container)) {
			$view = 'river/user/default/profileiconupdate';
			elgg_delete_river(['subject_guid' => $container->guid, 'view' => $view]);
			elgg_create_river_item([
				'view' => $view,
				'action_type' => 'update',
				'subject_guid' => $container->guid,
				'object_guid' => $container->guid,
			]);
		}
	}
	system_message(elgg_echo('avatar:crop:success'));
} else {
	register_error(elgg_echo('avatar:crop:fail'));
}

forward(REFERRER);

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

if (isset($params->container_guid)) {
	$container = get_entity($params->container_guid);
} else {
	$container = elgg_get_logged_in_user_entity();
}

if (!$container instanceof ElggEntity) {
	register_error(elgg_echo('avatars:error:not_found'));
	forward(REFERRER);
}

if (!$container->canEdit() || !$container->canWriteToContainer(0, 'object', hypeJunction\Images\Avatar::SUBTYPE)) {
	register_error(elgg_echo('avatars:error:permission_denied'));
	forward(REFERRER);
}

$avatar = avatars_create_avatar_from_upload($container, 'avatar');
if ($avatar) {
	$container->icontime = $avatar->time_created;
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

	system_message(elgg_echo('avatar:upload:success'));
	forward("avatars/edit/$avatar->guid?tab=crop");
} else {
	register_error(elgg_echo('avatars:upload:fail'));
	forward(REFERRER);
}


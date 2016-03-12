<?php

use hypeJunction\Images\Avatar;

/**
 * Avatar
 *
 * @author Ismayil Khayredinov <info@hypejunction.com>
 * @copyright Copyright (c) 2015, Ismayil Khayredinov
 */
require_once __DIR__ . '/autoloader.php';

elgg_register_event_handler('init', 'system', 'avatars_init');

/**
 * Initialize the plugin
 * @return void
 */
function avatars_init() {
	
	elgg_register_page_handler('avatars', 'avatars_page_handler');

	elgg_register_action('avatars/upload', __DIR__ . '/actions/avatars/upload.php');
	elgg_register_action('avatars/crop', __DIR__ . '/actions/avatars/crop.php');
}

/**
 * Check if avatars enabled for entities of given type and subtype
 * 
 * @param string $type    Entity type
 * @param string $subtype Entity subtype
 * @return bool
 */
function avatars_enabled($type, $subtype = null) {
	$hook_type = $type;
	if ($subtype) {
		$hook_type = "$type:$subtype";
	}
	return elgg_trigger_plugin_hook('avatars:enabled', $hook_type, null, false);
}

/**
 * Create an avatar object from an upload
 *
 * @param ElggEntity $entity     Entity to which avatar will belong
 * @param string     $input_name Input name
 * @return Avatar|false
 */
function avatars_create_avatar_from_upload(ElggEntity $entity, $input_name = 'avatar') {
	return elgg_images_create_avatar_from_upload($entity, $input_name);
}

/**
 * Create an avatar from a file resource
 * 
 * @param ElggEntity $entity Entity to which avatar will belong
 * @param type       $path   Path to file
 * @return Avatar|false
 */
function avatars_create_avatar_from_resource(ElggEntity $entity, $path) {
	return elgg_images_create_avatar_from_resource($entity, $path);
}

/**
 * Clear all entity avatars
 *
 * @param ElggEntity $entity Entity
 * @return void
 */
function avatars_clear_avatars(ElggEntity $entity) {
	return elgg_images_clear_avatars($entity);
}

/**
 * Returns entity avatar
 *
 * @param ElggEntity $entity Entity
 * @return Avatar|false
 */
function avatars_get_avatar(ElggEntity $entity) {
	return elgg_images_get_avatar($entity);
}

/**
 * Page handler
 *
 * @param array  $segments   URL segments
 * @param string $identifier Page Identifier
 * @return bool
 */
function avatars_page_handler($segments, $identifier) {

	$page = array_shift($segments);

	switch ($page) {
		case 'upload' :
			echo elgg_view("resources/avatars/upload", [
				'container_guid' => $segments[0],
				'identifier' => $identifier,
			]);
			return true;

		case 'edit' :
			echo elgg_view("resources/avatars/edit", [
				'guid' => $segments[0],
				'identifier' => $identifier,
			]);
			return true;
	}

	return false;
}

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

	elgg_register_plugin_hook_handler('entity:url', 'object', 'avatars_entity_url_handler');
	elgg_register_plugin_hook_handler('entity:icon:url', 'all', 'avatars_entity_icon_url_handler');

	elgg_register_event_handler('update:after', 'all', 'avatars_update_avatar_access');

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
 * Avatar URLs
 * 
 * @param string $hook   "entity:url"
 * @param string $type   "object"
 * @param string $return URL
 * @param array  $params Hook params
 * @return string
 */
function avatars_entity_url_handler($hook, $type, $return, $params) {

	$entity = elgg_extract('entity', $params);
	if ($entity instanceof Avatar) {
		return "avatars/view/$entity->guid";
	}
}

/**
 * Create an avatar object from an upload
 *
 * @param ElggEntity $entity     Entity to which avatar will belong
 * @param string     $input_name Input name
 * @return Avatar|false
 */
function avatars_create_avatar_from_upload(ElggEntity $entity, $input_name = 'avatar') {

	if (!empty($_FILES[$input_name]['name']) && $_FILES[$input_name]['error'] == UPLOAD_ERR_OK && substr_count($_FILES[$input_name]['type'], 'image/')) {

		avatars_clear_avatars($entity);

		$avatar = new Avatar();
		$avatar->owner_guid = $entity instanceof ElggUser ? $entity->guid : $entity->owner_guid;
		$avatar->container_guid = $entity->guid;
		$avatar->access_id = $entity->access_id;

		$avatar->setFilename("avatars/$entity->guid/" . time() . $_FILES[$input_name]['name']);

		$avatar->open('write');
		$avatar->close();
		move_uploaded_file($_FILES[$input_name]['tmp_name'], $avatar->getFilenameOnFilestore());

		$avatar->mimetype = ElggFile::detectMimeType($_FILES[$input_name]['tmp_name'], $_FILES[$input_name]['type']);
		$avatar->simpletype = 'image';
		$avatar->originafilename = $_FILES[$input_name]['name'];
		$avatar->title = $avatar->originalfilename;

		if (!$avatar->exists() || !$avatar->save()) {
			$avatar->delete();
			return false;
		}

		$entity->avatar_last_modified = $avatar->time_created;
		return $avatar;
	}

	return false;
}

/**
 * Clear all entity avatars
 *
 * @param ElggEntity $entity Entity
 * @return void
 */
function avatars_clear_avatars(ElggEntity $entity) {
	$avatars = elgg_get_entities([
		'types' => 'object',
		'subtypes' => Avatar::SUBTYPE,
		'container_guids' => (int) $entity->guid,
		'limit' => 0,
	]);

	if ($avatars) {
		foreach ($avatars as $avatar) {
			$avatar->delete();
		}
	}

	unset($entity->avatar_last_modified);
}

/**
 * Returns entity avatar
 *
 * @param ElggEntity $entity Entity
 * @return Avatar|false
 */
function avatars_get_avatar(ElggEntity $entity) {

	if (!$entity->avatar_last_modified) {
		return false;
	}

	$avatars = elgg_get_entities([
		'types' => 'object',
		'subtypes' => Avatar::SUBTYPE,
		'container_guids' => $entity->guid,
		'limit' => 1,
	]);
	return !empty($avatars) ? $avatars[0] : false;
}

/**
 * Replace entity icon URL if entity has an avatar
 *
 * @param string $hook   "entity:icon:url"
 * @param string $type   "all"
 * @param string $return Icon URL
 * @param array  $params Hook params
 * @return array
 */
function avatars_entity_icon_url_handler($hook, $type, $return, $params) {

	$entity = elgg_extract('entity', $params);
	$size = elgg_extract('size', $params);

	if (!avatars_enabled($entity->getType(), $entity->getSubtype())) {
		return;
	}
	
	$avatar = avatars_get_avatar($entity);
	if ($avatar) {
		return $avatar->getIconURL($size);
	}
}

/**
 * Update avatar access id when entity is saved
 *
 * @param string     $event  "update:after"
 * @param string     $type   "
 * @param ElggEntity $entity
 */
function avatars_update_avatar_access($event, $type, $entity) {

	$access_id = (int) $entity->access_id;
	$avatars = elgg_get_entities([
		'types' => 'object',
		'subtypes' => Avatar::SUBTYPE,
		'container_guids' => (int) $entity->guid,
		'limit' => 0,
		'wheres' => [
			"e.access_id != $access_id"
		],
	]);

	foreach ($avatars as $avatar) {
		$avatar->access_id = $access_id;
		$avatar->save();
	}
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

		case 'view' :
			echo elgg_view("resources/avatars/view", [
				'guid' => $segments[0],
				'identifier' => $identifier,
			]);
			return true;
	}

	return false;
}

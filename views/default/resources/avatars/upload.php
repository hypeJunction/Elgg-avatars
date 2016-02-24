<?php

elgg_push_context('avatars/upload');

$container_guid = elgg_extract('container_guid', $vars);
if (!$container_guid) {
	$container_guid = elgg_get_logged_in_user_guid();
}

$container = get_entity($container_guid);
if (!$container) {
	forward('', '404');
}

if (!avatars_enabled($container->getType(), $container->getSubtype()) || !$container->canWriteToContainer(0, 'object', hypeJunction\Images\Avatar::SUBTYPE)) {
	forward('', '403');
}

elgg_set_page_owner_guid($container->guid);

elgg_group_gatekeeper();

elgg_push_breadcrumb(elgg_echo('avatars'), '/avatars/all');
elgg_push_breadcrumb($container->getDisplayName(), $container->getURL());

$title = elgg_echo('avatars:upload');
elgg_push_breadcrumb($title);

if (elgg_is_sticky_form('avatars/upload')) {
	$sticky_values = elgg_get_sticky_values('avatars/upload');
	if (is_array($sticky_values)) {
		$vars = array_merge($vars, $sticky_values);
	}
}
$vars['container_guid'] = $container->guid;

$content = elgg_view_form('avatars/upload', [
	'enctype' => 'multipart/form-data',
	'validate' => true,
		], $vars);

$body = elgg_view_layout('content', [
	'content' => $content,
	'title' => $title,
	'filter' => elgg_view('filters/avatars/edit', [
		'filter_context' => 'upload',
		'container' => $container,
	]),
		]);

echo elgg_view_page($title, $body);

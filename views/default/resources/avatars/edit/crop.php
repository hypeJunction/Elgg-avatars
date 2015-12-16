<?php

elgg_push_context('avatars/crop');

$guid = elgg_extract('guid', $vars);
$entity = get_entity($guid);

if (!$entity instanceof hypeJunction\Images\Avatar) {
	forward('', '404');
}

if (!$entity->canEdit()) {
	forward('', '403');
}

$container = $entity->getContainerEntity();

elgg_set_page_owner_guid($container->guid);

elgg_group_gatekeeper();

elgg_push_breadcrumb(elgg_echo('avatars'), '/avatars/all');
if ($container) {
	elgg_push_breadcrumb($container->getDisplayName(), $entity->getURL());
}
elgg_push_breadcrumb(elgg_echo('avatar'), $entity->getURL());

$title = elgg_echo('avatars:edit');
elgg_push_breadcrumb($title);

if (elgg_is_sticky_form('avatars/crop')) {
	$sticky_values = elgg_get_sticky_values('avatars/crop');
	if (is_array($sticky_values)) {
		$vars = array_merge($vars, $sticky_values);
	}
}
$vars['entity'] = $entity;

$content = elgg_view_form('avatars/crop', [], $vars);

$body = elgg_view_layout('content', [
	'content' => $content,
	'title' => $title,
	'filter' => elgg_view('filters/avatars/edit', [
		'filter_context' => 'crop',
	]),
		]);

echo elgg_view_page($title, $body);

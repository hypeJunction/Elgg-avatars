<?php

elgg_push_context('avatars/view');

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

$title = elgg_echo('avatar');
elgg_push_breadcrumb($title);

$content = elgg_view_entity($entity, array(
	'full_view' => true,
		));

$body = elgg_view_layout('content', [
	'content' => $content,
	'title' => $title,
	'filter' => '',
		]);

echo elgg_view_page($title, $body);

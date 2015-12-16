<?php

elgg_push_context('avatars/edit');

$tab = get_input('tab', 'upload');
$view = "resources/avatars/edit/$tab";

if (!elgg_view_exists($view)) {
	forward('', '404');
}

echo elgg_view($view, $vars);
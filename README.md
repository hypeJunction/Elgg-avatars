Entity Avatars for Elgg
=======================
![Elgg 1.11](https://img.shields.io/badge/Elgg-1.11.x-orange.svg?style=flat-square)
![Elgg 1.12](https://img.shields.io/badge/Elgg-1.12.x-orange.svg?style=flat-square)
![Elgg 2.0](https://img.shields.io/badge/Elgg-2.0.x-orange.svg?style=flat-square)

*ABANDONED*
The plugin had a serious negative impact on performance, so I have abandoned it.
Use https://github.com/hypeJunction/hypeLists

## Features

 * Generic API and UI for uploading, handling and cropping entity avatars

![Avatar UI](https://raw.github.com/hypeJunction/Elgg-avatars/master/screenshots/avatar_ui.png "Avatar UI")

## Usage

### Allow avatars

To allow entities to have avatars, use `'avatars:enabled', "$type:$subtype"` hook.

### Input Field

```php
echo elgg_view('input/avatar', array(
	'entity' => $entity,
	'name' => 'upload',
));
```

### Action

```php
avatars_create_avatar_from_upload($entity, 'upload');
```

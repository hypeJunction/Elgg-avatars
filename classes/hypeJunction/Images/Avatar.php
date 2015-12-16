<?php

namespace hypeJunction\Images;

class Avatar extends \hypeJunction\Images\Image {

	const SUBTYPE = 'avatar';
	
	/**
	 * Initialize object attributes
	 * @return void
	 */
	protected function initializeAttributes() {
		parent::initializeAttributes();
		$this->attributes['subtype'] = self::SUBTYPE;
		$this->attributes['access_id'] = ACCESS_PRIVATE;
	}
}

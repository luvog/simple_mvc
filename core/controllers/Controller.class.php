<?php

namespace core\controllers;

abstract class Controller {

	abstract public function defaultAction( array $args );

	public function errorAction( array $args = array() ) {
		return json_encode( $args );
	}
}

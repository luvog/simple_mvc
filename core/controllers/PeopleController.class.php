<?php

namespace core\controllers;

use core\models\{PersonModel,ColorModel};

class PeopleController extends Controller {

	public function defaultAction( array $args = array() ) {
		return $this->getAllAction( $args );
	}

	public function getAllAction( array $args = array() ): string {
		try {
			$mPerson = new PersonModel();
			$people = $mPerson->getAll();
			foreach( $people as $person ) {
				$person->eye_color = ColorModel::getByPk( $person->eye_color_id )->color;
				$person->car_color = ColorModel::getByPk( $person->car_color_id )->color;
				$person->house_color = ColorModel::getByPk( $person->house_color_id )->color;
			}

			return json_encode( $people );
		} catch( \Exception $e ) {
			$args['error'] = $e;
			return $this->errorAction( $args );
		}
	}

	public function searchByColorAction( array $args = array() ): string {
		try {
			if( !isset( $args['color'] ) || empty( $args['color'] ) )
				throw new \Exception( 'Color arg is mandatory' );

			$colorIds = array();
			$mColor = new ColorModel();
			$colors = $mColor->getByLikeName( $args['color' ] );
			foreach( $colors as $color ) {
				array_push( $colorIds, $color->color_id );
			}

			$mPerson = new PersonModel();
			$people = $mPerson->getByAllColors( $colorIds );

			foreach( $people as $person ) {
				$person->eye_color = ColorModel::getByPk( $person->eye_color_id )->color;
				$person->car_color = ColorModel::getByPk( $person->car_color_id )->color;
				$person->house_color = ColorModel::getByPk( $person->house_color_id )->color;
			}

			return json_encode( $people );
		} catch( \Exception $e ) {
			throw $e;
			$args['error'] = $e;
			return $this->errorAction( $args );
		}
	}
}

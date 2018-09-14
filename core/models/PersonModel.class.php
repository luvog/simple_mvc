<?php

namespace core\models;

class PersonModel extends Model {

	protected static $table = 'people';

	protected $data = array(
                'person_id' => null,
                'name' => null,
		'eye_color_id' => null,
		'car_color_id' => null,
		'house_color_id' => null,
		'eye_color' => null,
		'car_color' => null,
		'house_color' => null
        );

	public function __construct( \stdClass $person = null ) {
                if( !is_null( $person ) ) {
                        $this->person_id = $person->person_id;
                        $this->name = $person->name;
			$this->eye_color_id = $person->eye_color_id;
			$this->car_color_id = $person->car_color_id;
			$this->house_color_id = $person->house_color_id;
                }
        }

	public function getAll(): array {
		$sql = sprintf( "select * from %s", self::$table );
		$results = self::$conn->query( $sql );
		foreach( $results as $key => $result ) {
                        $results[$key] = new static( $result );
                }

                return $results;
	}

	public function getByAllColors( array $colorIds ): array {
		$ids = implode( "','", $colorIds );
		$sql = sprintf( "select * from %s where (eye_color_id in ('%s') or car_color_id in ('%s') or house_color_id in ('%s'))", self::$table, $ids, $ids, $ids );
		$results = self::$conn->query( $sql );
		foreach( $results as $key => $result ) {
                        $results[$key] = new static( $result );
                }

                return $results;
	}
}

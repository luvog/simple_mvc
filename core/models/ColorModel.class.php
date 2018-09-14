<?php

namespace core\models;

class ColorModel extends Model {

	protected static $table = 'color';

	protected $data = array(
		'color_id' => null,
		'color'	=> null
	);

	public function __construct( \stdClass $color = null ) {
		if( !is_null( $color ) ) {
			$this->color_id = $color->color_id;
			$this->color = $color->color;
		}
	}

	public static function getByPk( int $colorId ): ?\stdClass {
		$sql = sprintf( "select * from %s where color_id = %s", self::$table, $colorId );
		$results = self::$conn->query( $sql );
		if( !empty( $results ) ) return array_pop( $results );
		return null;
	}

	public function getByLikeName( string $colorName ): array {
		$sql = sprintf( "select * from %s where color like '%s'", self::$table, "%$colorName%" );
		$results = self::$conn->query( $sql );
		foreach( $results as $key => $result ) {
			$results[$key] = new static( $result );
		}

		return $results;
	}
}

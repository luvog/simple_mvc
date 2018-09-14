<?php

namespace core\models;

use core\sql\MySQLDataBase;

class Model implements \JsonSerializable {

	protected static $conn = null;
	protected $data = array();

	public static function setConn( $conn ) {
		self::$conn = $conn;
	}
	
	public function __get( $property ) {
	        if( !array_key_exists( $property, $this->data ) )
			throw new \Exception( sprintf( 'Property not defined in class %s', self::class ) );

		return $this->data[$property];
        }

	public function __set( $property, $value ) {
	        if( !array_key_exists( $property, $this->data ) )
			throw new \Exception( sprintf( 'Property not defined in class %s', self::class ) );

		$this->data[$property] = $value;
	}

	public function execute() {
		$this->conn->execute();
	}

	public function jsonSerialize() {
		$data = get_object_vars( $this );
		return $data['data'];
	}
}

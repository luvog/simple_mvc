<?php

namespace core\sql;

use core\Config;

class MySQLDataBase implements \core\interfaces\DataAccessInterface {

	protected $db = null;

	public function __construct() {
		$this->connect( Config::getModuleConfig( '$database' ) );
	}

	public function connect( \stdClass $config ): void {
		$this->db = new \mysqli( $config->db_host, $config->db_user, $config->db_pass, $config->db_data );
		$this->db->autocommit(false);
	}

        public function disconnect(): void {
		$this->db->close();
	}

        public function execute() {

	}

        public function query( string $sql ): array {
		$array = array();
		$result = $this->db->query( $sql );
		
		while( $obj = $result->fetch_object() ) {
			array_push( $array, $obj );
		}

		return $array;
	}
}

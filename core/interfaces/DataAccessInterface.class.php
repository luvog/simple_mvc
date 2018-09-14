<?php

namespace core\interfaces;

interface DataAccessInterface {
	public function connect( \stdClass $config ): void;
	public function disconnect(): void;
	public function execute();
	public function query( string $sql );
}

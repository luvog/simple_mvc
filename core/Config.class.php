<?php

namespace core;

class Config {
	const CONFIG_ROUTE = 'config/config.json';

	protected static $config = null;

	public static function getModuleConfig( string $module ): \stdClass {
		try {
			$configText = file_get_contents( self::CONFIG_ROUTE );
			$globalConfig = json_decode( $configText );
			return $globalConfig->modules->{$module};
		} catch( \Exception $e ) {
			throw new \Exception( 'No config for app provided' );
		}		
	}
}

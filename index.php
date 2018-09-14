<?php

use core\sql\MySQLDataBase;
use core\models\Model;
use core\controllers\Controller;

class App {

        protected $components = array( 'views', 'models', 'controllers' );
        protected $namespaces = array( 'core' );

	public function __construct() {
		header( 'Access-Control-Allow-Origin: *' );
		header( 'Content-Type: application/json' );
		spl_autoload_register( array( $this, 'getPath' ) );
		set_error_handler( array( $this, 'errorIntoExceptionHandler' ), E_ALL );

		try {
			Model::setConn( new MySQLDataBase() );
		} catch( \Exception $e ) {
			throw new \Exception( $e->getMessage() );
		}
	}

	public function getPath( string $className ): void {
		$filepath = __DIR__.str_replace('\\', '/', "/$className.class.php" );
		if( file_exists( $filepath ) ) { 
			require_once( $filepath );
		}
	}

	public function parseURL( string $url ): array {
		$urlParams = array_filter( explode( '/', $url ) );
		if( sizeof( $urlParams ) < 1 ) throw new \Exception( 'Bad request' );

		$callParams = array();
		$callParams['controller'] = $this->getController( $urlParams );
		$callParams['method'] = $this->getMethod( $urlParams );
		$callParams['args'] = $this->getArgs( $urlParams );

		return $callParams;
	}

	private function getController( array &$urlParams ): string {
		$classes = array();
		$controllerName = ucfirst( array_shift( $urlParams ) ).'Controller';
		foreach( $this->namespaces as $namespace ) {
			$class = "$namespace\\controllers\\$controllerName";
			array_push( $classes, addslashes( $class ) );
			$filepath = __DIR__."/$namespace/controllers/$controllerName.class.php";
			if( file_exists( $filepath ) ) {
				return $class;
			}
		}
		throw new \Exception( "The class $controllerName is not defined in [".implode( '], [', $classes )."]" );
	}

	private function getMethod( array &$urlParams ): string {
		$methodName = ucwords( array_shift( $urlParams ), '-' );
		if( empty( $methodName ) ) return 'defaultAction';
		return lcfirst( str_replace( '-', '', $methodName.'Action' ) );
	}

	private function getArgs( array $urlParams ): array {
		$args = array();
		foreach( $urlParams as $urlParam ) {
			list( $key, $value ) = explode( '_', $urlParam );
			$args[$key] = $value;
		}
		return $args;
	}

        public function getInstance( string $className ): Controller {
		$class = new \ReflectionClass( $className );
		return $class->newInstance();
        }

	public function errorIntoExceptionHandler( $errno, $errstr, $errfile, $errline ) {
		throw new \ErrorException( $errstr, 0, $errno, $errfile, $errline );
	}
}

try {
	$app = new App();
	$callParams = $app->parseURL( $_SERVER["REQUEST_URI"] );
	$controller = $app->getInstance( $callParams['controller'] );
	$method = $callParams['method'];
	if( method_exists( $controller, $method ) && is_callable( array( $controller, $method ) ) )
		$result = $controller->{$callParams['method']}( $callParams['args'] );
	else 
		throw new \Exception( 'The method you are trying to call no exists or is private' );

	echo $result;
} catch( \Exception $e ) {
	echo sprintf(
		'{ "error": { "message": "%s", "status": 500, "trace": %s } }', 
		$e->getMessage(), 
		json_encode( $e->getTrace() ) 
	);
}

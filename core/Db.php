<?php

namespace core;

use System\Config;

class Db extends \PDO
{
	//protected static $instance;
	
	public function __construct( $dsn, $user = '', $password = '' )
	{
		try {
			
			parent::__construct( $dsn, $user, $password, [
				\PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
				\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
			] );
			
		} catch( \PDOException $pdo ) {
			
			exit( $pdo->getMessage() );
			
		}
		
	}
	
	/*
	public static function getInstance()
	{
		if( is_null( static::$instance ) ) static::$instance = new Db();
		
		return static::$instance;
	}
	*/
	
}
<?php

namespace App\Helpers\API\Superlogica;

/**
* Conexão com o banco
*/
class Database
{
	private static $connection = null;
	// private static $user 	= "lifepetcombr";
	// private static $database = "lifepetcombr";
	// private static $password = "tQcGLYl7Zs1YBbaC";
	// private static $host 	= "127.0.0.1";
	// 
	// Alternative
	private static $user 	= "sql10183031";
	private static $database = "sql10183031";
	private static $password = "udLE1rUglE";
	private static $host 	= "sql10.freemysqlhosting.net";

	public static function getConnection() {
		if(!self::$connection) {
			self::$connection = self::open();
		}

		return self::$connection;
	}

	public static function open() {
		return new \PDO(
			'mysql:host=' . self::$host . ';' .
			'dbname=' . self::$database, 
			self::$user, 
			self::$password
		);
	}
}
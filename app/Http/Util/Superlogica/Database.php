<?php

namespace App\Http\Util\Superlogica;

/**
* Conexão com o banco
*/
class Database
{
	private static $connection = null;
	private static $user = "root";
	private static $password = "";

	public static function getConnection() {
		if(!self::$connection) {
			self::$connection = self::open();
		}

		return self::$connection;
	}

	public static function open() {
		return new \PDO('mysql:host=localhost;dbname=lifepet', self::$user, self::$password);
	}
}
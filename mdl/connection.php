<?php
namespace mdl;

use \PDO       as PDO;
use \Exception as Exception;

class connection extends PDO 
{
	static private ?connection $instance = null;

	public static function get()
	{
		if(is_null(self::$instance)) {
			self::$instance = new connection;
		}
		return self::$instance;
	} 

	private function __construct()
	{
		$cwd  = $_SERVER['DOCUMENT_ROOT'];
		$path = $cwd . '/art.db';

		parent::__construct('sqlite:' . $path);
	}

	private function drop(string $table_name) : bool
	{
		try {
			$this->exec('DROP TABLE `' . $table_name . '`;');
			return true;
		} catch(Exception $e) {
			error_log($e->getMessage());
			return false;
		}
	}

	public function migrate() : void
	{
		$this->drop('users');
		$this->drop('pictures');
		$this->drop('comments');

		$this->exec('CREATE TABLE `users` (
			id INTEGER PRIMARY KEY AUTOINCREMENT,
			username VARCHAR,
			password VARCHAR
		);');

		$this->exec('CREATE TABLE `pictures` (
			id INTEGER PRIMARY KEY AUTOINCREMENT,
			userid INTEGER NOT NULL,
			date   DATETIME,
			data   VARCHAR
		);');

		$this->exec('CREATE TABLE `comments` (
			id        INTEGER PRIMARY KEY AUTOINCREMENT,
			userid    INTEGER NOT NULL,
			pictureid INTEGER NOT NULL,
			date      DATETIME,
			message   VARCHAR
		);');

		/* add some test users */
		user::insert("jack", "password");
	}
}
?>

<?php

namespace DB;

use \PDO       as PDO;
use \Exception as Exception;

class Connection extends PDO 
{
	private static ?Connection $instance = null;

	public static function get()
	{
		if(is_null(self::$instance)) {
			self::$instance = new Connection;
		}
		return self::$instance;
	} 

	private function __construct()
	{
		parent::__construct("sqlite:art.db");
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
		User::insert("jack", "password");
	}

	public function add_picture(string $picture_data) : bool
	{
		$user = $_SESSION['user'];
		$stmt = $this->prepare('INSERT INTO `pictures` (userid, date, data) (?,datetime("now"),?);');

		return $stmt->execute([$user->id, $picture_data]);

		return true;
	}
}
?>

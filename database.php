<?php

class database_connection extends PDO 
{
	function __construct()
	{
		parent::__construct("sqlite:art.db");
	}

	public function migrate() : void
	{
		try {
			$this->exec('DROP TABLE `users`');
			$this->exec('DROP TABLE `pictures`');
		} catch(Exception $e) {
			error_log($e->getMessage());
		}

		$this->exec('CREATE TABLE `users` (
			id INTEGER PRIMARY KEY AUTOINCREMENT,
			username VARCHAR,
			password VARCHAR
		);');

		$this->exec('CREATE TABLE `pictures` (
			id INTEGER PRIMARY KEY AUTOINCREMENT,
			userid INTEGER NOT NULL,
			bitmap BLOB
		);');

		/* add some test users */
		$this->signup("jack", "password");
	}

	public function signup(string $username, string $password) : bool
	{
		$password_hash = password_hash($password, PASSWORD_BCRYPT);

		$stmt = $this->prepare('INSERT INTO `users` (username, password) VALUES (?,?);');

		return $stmt->execute([$username, $password_hash]);
	}

	public function login(string $username, string $password) : bool
	{
		$stmt = $this->prepare('SELECT id, password FROM `users` WHERE username=?');

		$stmt->execute([$username]);
		$user = $stmt->fetch();

		return password_verify($password, $user['password']);
	}
}


?>

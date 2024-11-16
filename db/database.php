<?php


class DatabaseConnection extends PDO 
{
	private static ?DatabaseConnection $instance = null;

	public static function get()
	{
		if(is_null(self::$instance)) {
			self::$instance = new DatabaseConnection;
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
			date      DATETIME
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

class User
{
	public  int    $id;
	public  string $username;
	private string $password;

	/* we take in a password that is already hashed */
	private function __construct($id, $username, $password)
	{
		$this->id       = $id;
		$this->username = $username;
		$this->password = $password;
	}

	public static function insert($username, $password) : ?User
	{
		$db = DatabaseConnection::get();
		$password_hash = password_hash($password, PASSWORD_BCRYPT);

		$stmt = $db->prepare('INSERT INTO `users` (username, password) VALUES (?,?);');

		if($stmt->execute([$username, $password_hash])) {
			return self::fetch($username);
		} else {
			return false;
		}
	}

	public static function fetch($username) : ?User
	{
		$db = DatabaseConnection::get();
		$stmt = $db->prepare('SELECT id, password FROM `users` WHERE username=?');
		$stmt->execute([$username]);
		$userdata = $stmt->fetch();

		$userid   = $userdata['id'];
		$password = $userdata['password'];

		if($userdata == FALSE) {
			return null;
		} else {
			return new User($userid, $username, $password);
		}
	}

	public function password_verify($password)
	{
		return password_verify($password, $this->password);
	}
}


?>

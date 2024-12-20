<?php
namespace mdl;

class user
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

	public static function insert(string $username, string $password) : ?user
	{
		$db = connection::get();
		$password_hash = password_hash($password, PASSWORD_BCRYPT);

		$stmt = $db->prepare('INSERT INTO `users` (username, password) VALUES (?,?);');

		if($stmt->execute([$username, $password_hash])) {
			return new user($db->lastInsertId(), $username, $password);
		} else {
			return null;
		}
	}

	public static function fetch(string $username) : ?user
	{
		$db = connection::get();
		$stmt = $db->prepare('SELECT id, password FROM `users` WHERE username=? LIMIT 1');
		$stmt->execute([$username]);
		$userdata = $stmt->fetch();

		if($userdata == FALSE) {
			return null;
		} else {
			$userid   = $userdata['id'];
			$password = $userdata['password'];
			return new user($userid, $username, $password);
		}
	}

	public static function from_id(int $id) : ?user 
	{
		$db = connection::get();
		$stmt = $db->prepare('SELECT username, password FROM `users` WHERE id=? LIMIT 1');
		$stmt->execute([$id]);
		$userdata = $stmt->fetch();

		if($userdata == FALSE) {
			return null;
		} else {
			$username = $userdata['username'];
			$password = $userdata['password'];
			return new user($id, $username, $password);
		}
	}

	public function password_verify(string $password)
	{
		return password_verify($password, $this->password);
	}
}

?>
<?php
namespace DB;
require_once 'db/connection.php';

class Picture
{
	public  int $id;
	private int $user_id;
	private string $data;

	private function __construct(int $id, User $user, string $data)
	{
		$this->id      = $id;
		$this->user_id = $user->id;
		$this->data    = $data;
	}

	static public function list_pictures(User $user) : array
	{
		$db = Connection::get();
		$stmt = $db->prepare('SELECT id, userid, data FROM `pictures` WHERE userid=?');

		if($stmt->execute([$user->id])) {
			return $stmt->fetch();
		} else {
			return array();
		}
	}

	static public function insert_picture(User $user, string $data) : ?Picture
	{
		$db = Connection::get();
		$stmt = $db->prepare('INSERT INTO `pictures` (userid, data) VALUES (?,?);');
		
		if($stmt->execute([$user->id, $data])) {
			$db->lastInsertId($stmt);
			return new Picture($db->lastInsertId(), $user, $data);
		} else {
			return null;
		}
		
	}
}
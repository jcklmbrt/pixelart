<?php
namespace DB;

use DateTime;

require_once 'db/connection.php';

class Comment
{
	public  int      $id;
	private int      $user_id;
	private int      $picture_id;
	private DateTime $date;
	private string   $message;

	private function __construct(int $id, User $user, Picture $picture, DateTime $date, string $message)
	{
		$this->id         = $id;
		$this->user_id    = $user->id;
		$this->picture_id = $picture->id;
		$this->date       = $date;
		$this->message    = $message;
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
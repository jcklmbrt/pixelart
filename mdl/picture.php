<?php
namespace mdl;

use \DateTime as DateTime;
use mdl\connection;
use mdl\user;

class picture
{
	public  int $id;
	private int $user_id;
	private DateTime $date;
	private string   $data;

	private function __construct(int $id, user $user, DateTime $date, string $data)
	{
		$this->id      = $id;
		$this->user_id = $user->id;
		$this->data    = $data;
	}

	static public function list_pictures(user $user) : array
	{
		$db = connection::get();
		$stmt = $db->prepare('SELECT id, userid, data FROM `pictures` WHERE userid=?');

		if($stmt->execute([$user->id])) {
			return $stmt->fetch();
		} else {
			return array();
		}
	}

	static public function insert_picture(user $user, string $data) : ?picture
	{
		$db = connection::get();
		$stmt = $db->prepare('INSERT INTO `pictures` (userid, date, data) (?,datetime("now"),?);');
		
		if($stmt->execute([$user->id, $data])) {
			return new picture($db->lastInsertId(), $user, new DateTime('now'), $data);
		} else {
			return null;
		}
	}
}
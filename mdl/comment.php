<?php
namespace mdl;
use \DateTime as DateTime;

class comment
{
	public int      $id;
	public int      $user_id;
	public int      $picture_id;
	public DateTime $date;
	public string   $message;

	private function __construct(int $id, user $user, picture $picture, DateTime $date, string $message)
	{
		$this->id         = $id;
		$this->user_id    = $user->id;
		$this->picture_id = $picture->id;
		$this->date       = $date;
		$this->message    = $message;
	}

	static public function list_comments(picture $picture) : array
	{
		$db = connection::get();
		$stmt = $db->prepare('SELECT id, userid, pictureid, date, message FROM `pictures` WHERE pictureid=?');

		if($stmt->execute([$picture->id])) {
			return $stmt->fetch();
		} else {
			return array();
		}
	}

	static public function comment(user $user, picture $picture, string $message) : ?comment
	{
		$db = connection::get();
		$message = htmlentities($message);

		$stmt = $db->prepare('INSERT INTO `comments` (userid, pictureid, date, message) VALUES (?,?,DATETIME("now"),?);');

		if($stmt->execute([$user->id, $picture->id, $message])) {
			return new comment($db->lastInsertId(), $user, $picture, new DateTime('now'), $message);
		} else {
			return null;
		}
	}
}
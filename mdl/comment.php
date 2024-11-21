<?php
namespace mdl;
use \DateTime as DateTime;

class comment
{
	public int      $id;
	public user     $user;
	public picture  $picture;
	public DateTime $date;
	public string   $message;

	private function __construct(int $id, user $user, picture $picture, DateTime $date, string $message)
	{
		$this->id      = $id;
		$this->user    = $user;
		$this->picture = $picture;
		$this->date    = $date;
		$this->message = $message;
	}

	static public function list_comments(picture $picture) : array
	{
		$db = connection::get();
		$stmt = $db->prepare('SELECT id, userid, pictureid, date, message FROM `comments` WHERE pictureid=? ORDER BY date DESC');

		$res = array();

		if($stmt->execute([$picture->id])) {
			$assoc = $stmt->fetchAll();
			if($assoc != FALSE) {
				foreach($assoc as $item) {
					$user = user::from_id($item['userid']);
					$comment = new comment($item['id'], $user, $picture, new DateTime($item['date']), $item['message']);
					array_push($res, $comment);
				}
			}
		}

		return $res;
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
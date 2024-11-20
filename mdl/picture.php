<?php
namespace mdl;

use \DateTime as DateTime;
use mdl\connection;
use mdl\user;

class picture
{
	public  int      $id;
	public user     $user;
	public DateTime $date;
	public string   $title;
	public string   $data;

	private function __construct(int $id, user $user, DateTime $date, string $title, string $data)
	{
		$this->id    = $id;
		$this->user  = $user;
		$this->title = $title;
		$this->data  = $data;
	}

	static public function list_pictures(user $user) : array
	{
		$db = connection::get();
		$stmt = $db->prepare('SELECT id, title, date, data 
		                      FROM `pictures`
		                      WHERE userid=?
				      ORDER BY date DESC');

		$res = array();

		if($stmt->execute([$user->id])) {

			$assoc = $stmt->fetchAll();
			
			foreach($assoc as $item) {
				$p = new picture($item['id'], $user, new DateTime($item['date']), $item['title'], $item['data']);
				array_push($res, $p);
			}
		}

		return $res;
	}

	static public function most_recent(int $num) : array
	{
		$db = connection::get();
		$stmt = $db->prepare('SELECT P.id, U.username, P.title, P.date, P.data 
		                      FROM `pictures` P, `users` U
				      WHERE U.id == P.userid
				      ORDER BY date DESC
				      LIMIT ?');

		$res = array();

		if($stmt->execute([$num])) {

			$assoc = $stmt->fetchAll();
			
			foreach($assoc as $item) {
				$user = user::fetch($item['username']);
				$p = new picture($item['id'], $user, new DateTime($item['date']), $item['title'], $item['data']);
				array_push($res, $p);
			}
		}

		return $res;
	}

	static public function insert(user $user, string $title, string $data) : ?picture
	{
		$db = connection::get();
		$stmt = $db->prepare('INSERT INTO `pictures` (userid, date, title, data) VALUES (?,datetime("now"),?,?);');
		
		if($stmt->execute([$user->id, $title, $data])) {
			return new picture($db->lastInsertId(), $user, new DateTime('now'), $title, $data);
		} else {
			return null;
		}
	}
}
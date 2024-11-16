<?php

session_start();

include_once "db/database.php";

define('USERNAME_MAX_LEN', 12);
define('USERNAME_MIN_LEN', 3);


function is_logged_in() : bool
{
	return isset($_SESSION['user']);
}

function is_valid_username(string $username) : bool
{
	$len = strlen($username);

	return $len < USERNAME_MAX_LEN && $len > USERNAME_MIN_LEN;
}

function is_valid_password(string $password) : bool
{
	return false;
}

function posted(string ...$items) : bool
{
	$r = true;
	foreach($items as $item) {
		$r = $r && isset($_POST[$item]);
	}
	return $r;
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
	if(posted("username", "password")) {
		$username = htmlentities($_POST['username']);
		$password = htmlentities($_POST['password']);

		$user = User::fetch($username);

		if($user->password_verify($password)) {
			$_SESSION['user'] = $user;
		} else {
			$GLOBALS['err'] = "test";
		}
	};
} else {
	/* logout */
	session_destroy();
}

header("location: /");

?>
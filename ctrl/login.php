<?php
set_include_path($_SERVER['DOCUMENT_ROOT']);
spl_autoload_extensions('.php');
spl_autoload_register();

use mdl\connection;
use mdl\user;
use ctrl\request;
use ctrl\session;

$s = new session;

define('USERNAME_MAX_LEN', 12);
define('USERNAME_MIN_LEN', 3);

function valid_username(string $username) : bool
{
	$len = strlen($username);

	return $len < USERNAME_MAX_LEN && $len > USERNAME_MIN_LEN;
}

function valid_password(string $password) : bool
{
	return false;
}

if(request::posted("username", "password")) {
	$username = htmlentities($_POST['username']);
	$password = htmlentities($_POST['password']);

	$user = user::fetch($username);

	if($user->password_verify($password)) {
		$s->set_local_user($user);
	} else {
		$GLOBALS['err'] = "test";
	}
} else {
	/* logout */
	$s->destroy();
}

header("location: /");

?>
<?php
set_include_path($_SERVER['DOCUMENT_ROOT']);
spl_autoload_extensions('.php');
spl_autoload_register();

use mdl\connection;
use mdl\user;
use ctrl\request;
use ctrl\session;

$s = new session;

const MAX_USERNAME = 12;
const MIN_USERNAME = 3;

const MIN_PASSWORD = 8;
const MAX_PASSWORD = 24;

function valid_username(session $s, string $username) : bool
{
	$len = strlen($username);
	$res = true;

	if($len > MAX_USERNAME) {
		$s->err_push('Username must be shorter than ' . MAX_USERNAME . ' chars');
		$res = false;
	}

	if($len < MIN_USERNAME) {
		$s->err_push('Username must be longer than ' . MIN_USERNAME . ' chars');
		$res = false;
	}

	if(!ctype_alnum($username)) {
		$s->err_push('Username must be alphanumeric');
		$res = false;
	}

	/* user already exists */
	if(!is_null(user::fetch($username))) {
		$s->err_push('A user with that name already exists');
		$res = false;
	}

	return $res;
}

function valid_password(session $s, string $password) : bool
{
	$len = strlen($password);
	$res = true;

	$onepunct = false;
	$onelet   = false;
	$onecap   = false;

	for($i = 0; $i < $len; $i++) {
		$ch = $password[$i];
		if(!ctype_alnum($ch)) {
			if(ctype_punct($ch) || ctype_space($ch)) {
				$onepunct = true;
			} else {
				$error = 'Invalid character in password: ' . $ch;
				return false;
			}
		} else {
			if(ctype_digit($ch)) { 
				$onelet = true; 
			} else if(!ctype_lower($ch)) {
				$onecap = true;
			}
		}
	}

	if($onepunct != true) {
		$s->err_push('Password must include at least one punctuation mark');
		$res = false;
	}

	if($onecap != true) {
		$s->err_push('Password must include at least one captial letter');
		$res = false;
	}

	if($onelet != true) {
		$s->err_push('Password must include at least one number');
		$res = false;
	}

	if($len > MAX_PASSWORD) {
		$s->err_push('Password must be shorter than ' . MAX_PASSWORD . ' chars');
		$res = false;
	}

	if($len < MIN_PASSWORD) {
		$s->err_push('Password must be longer than ' . MIN_PASSWORD . ' chars');
		$res = false;
	}

	return $res;
}


if(request::posted("username", "password", "password2")) {
	$username  = htmlentities($_POST['username']);
	$password  = htmlentities($_POST['password']);
	$password2 = htmlentities($_POST['password2']);

	$valid = true;

	if($password != $password2) {
		$s->err_push('Passwords do not match');
		$valid = false;
	}

	$valid &= valid_username($s, $username);
	$valid &= valid_password($s, $password);

	if($valid) {
		$user = user::insert($username, $password);

		if(is_null($user)) {
			$s->err_push('Database error. Try again later');
		} else {
			$s->set_local_user($user);
			$s->set_page('home');
		}
	}
}

request::relocate("/");

?>
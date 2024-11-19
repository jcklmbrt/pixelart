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

function valid_username(string $username, &$error) : bool
{
	$len = strlen($username);

	if(!ctype_alnum($username)) {
		$error = 'Username must be alphanumeric';
		return false;
	}

	if($len > MAX_USERNAME) {
		$error = 'Username must be shorter than ' . MAX_USERNAME . ' chars';
		return false;
	}

	if($len < MIN_USERNAME) {
		$error = 'Username must be longer than ' . MIN_USERNAME . ' chars';
		return false;
	}

	return true;
}

function valid_password(string $password, &$error) : bool
{
	$len = strlen($password);

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
		$error = 'Password must include at least one punctuation mark';
		return false;
	}

	if($onecap != true) {
		$error = 'Password must include at least one captial letter';
		return false;
	}

	if($onelet != true) {
		$error = 'Password must include at least one number';
		return false;
	}

	if($len > MAX_PASSWORD) {
		$error = 'Password must be shorter than ' . MAX_PASSWORD . ' chars';
		return false;
	}

	if($len < MIN_PASSWORD) {
		$error = 'Password must be longer than ' . MIN_PASSWORD . ' chars';
		return false;
	}

	return true;
}

function set_error($msg) : void 
{
	request::push_get('register_error', $msg);
	request::relocate('/');
}

if(request::posted("username", "password", "password2")) {
	$username  = htmlentities($_POST['username']);
	$password  = htmlentities($_POST['password']);
	$password2 = htmlentities($_POST['password2']);

	if($password != $password2) {
		set_error('Passwords do not match');
	}

	if(!valid_username($username, $error)) {
		set_error($error);
	}

	if(!valid_password($password, $error)) {
		set_error($error);
	}

	$user = user::insert($username, $password);

	if(is_null($user)) {
		set_error('A user with that name already exists');
	}

	$s->set_local_user($user);
}

request::relocate("/");

?>
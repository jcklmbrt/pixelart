<?php
set_include_path($_SERVER['DOCUMENT_ROOT']);
spl_autoload_extensions('.php');
spl_autoload_register();

use mdl\connection;
use mdl\user;
use ctrl\request;
use ctrl\session;

$s = new session;

function set_error($msg) {
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

	$user = user::insert($username, $password);

	/* assuming if stmt will short-circuit */
	if(!is_null($user) && $user->password_verify($password)) {
		$s->set_local_user($user);
	} else {
	}
		request::push_get('login_error', 'Invalid username or password');
} else {
	/* logout */
	$s->destroy();
}

request::relocate("/");

?>
<?php
set_include_path($_SERVER['DOCUMENT_ROOT']);
spl_autoload_extensions('.php');
spl_autoload_register();

use mdl\connection;
use mdl\user;
use ctrl\request;
use ctrl\session;

$s = new session;

if(request::posted("username", "password")) {
	$username = htmlentities($_POST['username']);
	$password = htmlentities($_POST['password']);

	$user = user::fetch($username);

	/* assuming if stmt will short-circuit */
	if(!is_null($user) && $user->password_verify($password)) {
		$s->set_local_user($user);
	} else {
		request::push_get('page', 'login');
		request::push_get('login_error', 'Invalid username or password');
	}
} else {
	/* logout */
	$s->destroy();
}

request::relocate("/");

?>
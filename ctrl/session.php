<?php
namespace ctrl;

use mdl\user;

class session
{
	function __construct() 
	{
		if(session_status() != PHP_SESSION_ACTIVE) {
			session_start();
		}
	}

	function logged_in() : bool
	{
		if(!isset($_SESSION['user'])) {
			return false;
		}

		return $_SESSION['user'] instanceof user;
	}

	function set_local_user(user $user)
	{
		$_SESSION['user'] = $user;
	}

	function get_local_user() : ?user
	{
		if($this->logged_in()) {
			return $_SESSION['user'];
		} else {
			return null;
		}
	}

	function destroy()
	{
		session_destroy();
	}
}
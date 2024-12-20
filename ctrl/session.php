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
		/* destroy session on DB update */
		if($this->logged_in()) {
			$user = $this->get_local_user();
			/* user no longer exists */
			if(is_null(user::fetch($user->username))) {
				$this->destroy();
				$this->__construct();
			}
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

	function set_page(string | user $page) 
	{
		$_SESSION['page'] = $page;
	}

	function page() : string | user
	{
		if(isset($_SESSION['page'])) {
			return $_SESSION['page'];
		} else {
			return 'home';
		}
	}

	function err_pop() : ?string
	{
		if(isset($_SESSION['err'])) {
			return array_pop($_SESSION['err']);
		} else {
			return null;
		}
	}

	function err_push(string $msg) : void
	{
		if(!isset($_SESSION['err'])) {
			$_SESSION['err'] = array();
		}
		array_push($_SESSION['err'], $msg);
	}

	function destroy()
	{
		session_destroy();
	}
}
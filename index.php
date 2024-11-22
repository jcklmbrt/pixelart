<?php

set_include_path($_SERVER['DOCUMENT_ROOT']);
spl_autoload_extensions('.php');
spl_autoload_register();

use ctrl\request;
use ctrl\session;
use mdl\picture;
use mdl\user;

$s = new session;

?>

<!DOCTYPE HTML>
<html>
	<head>
		<link rel="stylesheet" href="stylesheet.css"></link>
		<meta name="viewport" content="width=device-width, initial-scale=0.75 user-scalable=no">
	</head>
	<body onload="main()">
		<?php

		$page = request::get('page');
		if(!is_null($page)) {
			$s->set_page($page);
			request::relocate('/');
			die;
		} else {
			$page = $s->page();
			if(is_null($page)) {
				$page = 'home';
			}

			$user = request::get('user');
			if(!is_null($user)) {
				$user = user::fetch($user);
				if(!is_null($user)) {
					$s->set_page($user);
					request::relocate('/');
					die;
				}
			}
		}

		include 'view/nav-bar.php';

		$pages = [
			'login'    => 'view/login-form.php',
			'register' => 'view/register-form.php',
			'new'      => 'view/editor.php',
			'home'     => 'view/gallery.php'
		];

		if($page instanceof user) {
			include 'view/gallery.php';
		} else if(isset($pages[$page])) {
			include $pages[$page];
		} else {
			/* unknown page, default to home */
			$s->set_page('home');
			request::relocate('/');
			die;
		}

		?>
	</body>
</html>

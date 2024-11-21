<?php

set_include_path($_SERVER['DOCUMENT_ROOT']);
spl_autoload_extensions('.php');
spl_autoload_register();

use ctrl\request;
use ctrl\session;
use mdl\picture;

$s = new session;

?>

<!DOCTYPE HTML>
<html>
	<head>
		<link rel="stylesheet" href="stylesheet.css"></link>
	</head>
	<body onload="main()">
		<?php

		$page = request::get('page');
		$user = request::get('user');

		include 'view/nav-bar.php';

		if(!is_null($page)) {
			switch($page) {
				case 'login':
				include 'view/login-form.php';
				break;
				case 'register':
				include 'view/register-form.php';
				break;
				case 'new':
					if($s->logged_in()) {
						include 'view/editor.php';
					} 
				break;
			}
		} else {

			if(is_null($user)) {
				include 'view/gallery.php';
			} else {
				include 'view/profile.php';
			}
		}

		?>
	</body>
</html>

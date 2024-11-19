<?php

set_include_path($_SERVER['DOCUMENT_ROOT']);
spl_autoload_extensions('.php');
spl_autoload_register();

use ctrl\session;
use mdl\connection;
use mdl\user;

$s  = new session;
$db = connection::get();

?>

<!DOCTYPE HTML>
<html>
	<head>
		<link rel="stylesheet" href="stylesheet.css"></link>
	</head>
	<body onload="main()">
		<?php

		if($s->logged_in()) {
			include 'view/nav-bar.php';
			include 'view/editor.php';
		} else {
			include 'view/login-form.php';
			include 'view/register-form.php';
		}

		?>
	</body>
</html>

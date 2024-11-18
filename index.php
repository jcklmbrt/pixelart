<?php

set_include_path($_SERVER['DOCUMENT_ROOT']);
spl_autoload_extensions('.php');
spl_autoload_register();

use ctrl\session;
use mdl\connection;
use mdl\user;

$s  = new session;
$db = connection::get();
//$db->migrate();

function is_logged_in() : bool
{
	return isset($_SESSION['user']);
}

function color_to_string(int $color)
{
	/* color encoding: 0bAARRGGBB */
	$blue  = ($color >> 0) & 3;
	$green = ($color >> 2) & 3;
	$red   = ($color >> 4) & 3;

	$blue  = ($blue  / 3.0) * 255;
	$green = ($green / 3.0) * 255;
	$red   = ($red   / 3.0) * 255;

	return "rgb(" . $red . "," . $green . "," . $blue . ")";
}

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
		}

		?>
	</body>
</html>

<?php

set_include_path($_SERVER['DOCUMENT_ROOT']);
spl_autoload_extensions('.php');
spl_autoload_register();

use mdl\connection;
use mdl\user;

session_start();

$db = connection::get();
$db->migrate();

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
		<script type="text/javascript" src="js/pixelart.js"></script>
	</head>
	<body onload="main()">
		<nav>
		<?php if(is_logged_in()) { ?>
			<?php $user = $_SESSION['user']; echo $user->username ?>
			<a href="profile.php">my profile</a>
			<a href="ctrl/login.php">logout</a>
		<?php } else { ?>
			<form method="POST" action="/ctrl/login.php">
				<?php 
				if(isset($GLOBALS['err'])) { 
					echo $GLOBALS['err'];
				} 
				?>
				<label>Username:</label><input type="text"     name="username"></input>
				<label>Password:</label><input type="password" name="password"></input>
				<input type="submit"></input>
			</form>
		<?php } ?>
		</nav>

		<?php if(is_logged_in()) { ?>
		<div class="container">
			<canvas id="canvas" width="350px" height="300px"></canvas>
			<div class="tools">
				<img src="img/pencil.png"       onclick="set_pencil()"></img>
				<img src="img/bucket.png"       onclick="set_bucket()"></img>
				<img src="img/color-picker.png" onclick="set_color_picker()"></img>
				<img src="img/undo.png"         onclick="canvas_undo()"></img>
				<img src="img/redo.png"         onclick="canvas_redo()"></img>
				<img src="img/reset.png"        onclick="canvas_reset()"></img>
				<button id="color"></button>
			</div>
			<table class="palette" style="display: none;">
				<tr>
				<?php for($i = 0; $i < 64; $i += 1) { ?>
					<?php if($i % 8 == 0) { echo "</tr><tr>"; } ?>
					<?php echo "<td style='background-color:" . color_to_string($i) . "' onclick='set_tool_color(" . $i . ")'></td>"; ?>
				<?php } ?>
				</tr>
			</table>
			<form class="savebox">
				<div class="savebox-container">
				<input type="text" name="title" placeholder="Title"></input>
				<input id="form_data" style="display:none;" type="text" name="image_data"></input>
				<input type="submit"></input>
				</div>
			</form>
		</div>
		<?php } ?>
		</body>
</html>

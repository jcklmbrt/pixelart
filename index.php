<?php

session_start();

require "database.php";

$db = new database_connection;

$db->migrate();

function bytecolor_to_string(int $color)
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
		<script type="text/javascript" src="pixelart.js"></script>
	</head>
	<body onload="main()">
		<nav>
		<?php if(isset($_SESSION['username'])) { ?>
			<?php print_r($_SESSION['username']); ?>
			<a href="login.php">logout</a>
		<?php } else { ?>
			<form method="POST" action="/login.php">
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
		</form>
		</nav>
		<div class="container">

			<canvas id="canvas" width="350px" height="300px"></canvas>

			<div class="tools">
				<img src="pencil.png" onclick="set_pencil()"></img>
				<img src="bucket.png" onclick="set_bucket()"></img>
				<img src="color-picker.png"  onclick="set_color_picker()"></img>
				<img src="reset.png" onclick="canvas_reset()"></img>
				<button id="color"></button>
			</div>

			<table class="palette" style="display: none;">
				<tr>
				<?php for($i = 0; $i < 64; $i += 1) { ?>
					<?php if($i % 8 == 0) { echo "</tr><tr>"; } ?>
					<?php echo "<td style='background-color:" . bytecolor_to_string($i) . "' onclick='set_pen_color(" . $i . ")'></td>"; ?>
				<?php } ?>
				</tr>
			</table>

		</div>
	</body>
</html>
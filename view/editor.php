<?php

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


<!-- Include the javascript required for the editor -->
<script type="text/javascript" src="js/pixelart.js"></script>

<div class="container">
	<canvas id="canvas" width="350px" height="300px"></canvas>
	<div class="tools">
		<img src="img/pencil.png"       onclick="set_pencil()"></img>
		<img src="img/bucket.png"       onclick="set_bucket()"></img>
		<img src="img/color-picker.png" onclick="set_color_picker()"></img>
		<button id="color"></button>
		<img src="img/undo.png"         onclick="canvas_undo()"></img>
		<img src="img/redo.png"         onclick="canvas_redo()"></img>
		<img src="img/reset.png"        onclick="canvas_reset()"></img>
		<img src="img/save.png" id="save"></img>
	</div>
	<table class="palette" style="display: none;">
		<tr>
		<?php for($i = 0; $i < 64; $i += 1) { ?>
			<?php if($i % 8 == 0) { echo "</tr><tr>"; } ?>
			<?= "<td style='background-color:" . color_to_string($i) . "' onclick='set_tool_color(" . $i . ")'></td>"; ?>
		<?php } ?>
		</tr>
	</table>
	<form class="savebox" style="display:none;" action="ctrl/submit.php" method="POST">
		<input type="text" name="title" placeholder="Title..."></input>
		<input id="form_data" style="display:none;" type="text" name="image_data"></input>
		<input type="submit" value="Save"></input>
	</form>
</div>
<?php
set_include_path($_SERVER['DOCUMENT_ROOT']);
spl_autoload_extensions('.php');
spl_autoload_register();

use ctrl\request;
use ctrl\session;
use mdl\picture;
use mdl\user;

$s = new session;

function verify_image_data(string $image_data) : bool 
{
	$canvas_height = 300;
	$canvas_width  = 350;
	$pixel_size    = 10;

	$w = $canvas_width  / $pixel_size;
	$h = $canvas_height / $pixel_size;

	$len = strlen($image_data);
	/* invalid size */
	if($len != $w * $h) {
		return false;
	}

	$min = ord('0');
	$max = ord('0') + 0b111111;

	for($i = 0; $i < $len; $i++) {
		$c = ord($image_data[$i]);
		/* not a base64 char */
		if($c > $max || $c < $min) {
			return false;
		}
	}
	
	return true;
}

if(request::posted('title', 'image_data')) {

	$user = $s->get_local_user();
	$title      = htmlentities($_POST['title']);
	$image_data = htmlentities($_POST['image_data']);

	if(verify_image_data($image_data)) {
		picture::insert($user, $title, $image_data);
	} else {
		request::push_get('err', 'bad image data');
	}
}

request::relocate('/');

?>
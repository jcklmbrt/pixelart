<?php
set_include_path($_SERVER['DOCUMENT_ROOT']);
spl_autoload_extensions('.php');
spl_autoload_register();

use ctrl\request;
use ctrl\session;
use mdl\picture;
use mdl\user;

$s = new session;

function image_data_to_bmp(string $data) : string
{
	$width  = 35;
	$height = 30;

	$pixels   = array();
	$padding  = $width % 4;
	$row_size = $width * 3 + $padding;
	$bmp_size = $row_size * $height;
	$hdr_size = 54;

	for($y = $height - 1; $y >= 0; $y--) {
		for($x = 0; $x < $width; $x++) {
			$color = ord($data[$y*$width+$x]) - ord('0');
			$blue  = ($color >> 0) & 3;
			$green = ($color >> 2) & 3;
			$red   = ($color >> 4) & 3;

			$blue  = ($blue  / 3.0) * 255;
			$green = ($green / 3.0) * 255;
			$red   = ($red   / 3.0) * 255;

			array_push($pixels, $blue);
			array_push($pixels, $green);
			array_push($pixels, $red);
		}
		/* pad row */
		for($i = 0; $i < $width % 4; $i++) {
			array_push($pixels, 0);
		}
	}

	return pack(
		/* BITMAPFILEHEADER */
		'v'. /* magic */
		'V'. /* file size */
		'V'. /* reserved */
		'V'. /* offset to bitmap data */
		/* BITMAPV5HEADER */
		'V'. /* Header Size */
		'V'. /* Width */
		'V'. /* Height */
		'v'. /* Planes */
		'v'. /* Bits per pixel */
		'V'. /* Compression */
		'V'. /* Image Size */
		'V'. /* X pixels per meter */
		'V'. /* Y pixels per meter */
		'V'. /* No. Colors used */
		'V'. /* No. Important colors */
		/* Pixel Array */
		'C' . $bmp_size,

		/* BITMAPFILEHEADER */
		0x4D42,                /* magic */
		$bmp_size + $hdr_size, /* file size */
		0,                     /* reserved */
		$hdr_size,             /* offset to data */
		
		/* BITMAPV5HEADER */
		40,        /* Header Size */
		$width,    /* Width */
		$height,   /* Height */
		0,         /* Planes */
		24,        /* Bits per pixel */
		0,         /* Compression */
		$bmp_size, /* Image Size */
		0, 0,      /* x,y Pixels per meter */
		0, 0,      /* No. Color Used,Important */

		/* Pixel Array */
		...$pixels
	);
}

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

const MAX_TITLE = 24;

if(request::posted('title', 'image_data')) {

	$user = $s->get_local_user();
	$title      = htmlentities($_POST['title']);
	$image_data = htmlentities($_POST['image_data']);

	if($title == "") {
		$title = "untitled";
	}

	if(strlen($title) > MAX_TITLE) {
		$title = substr($title, 0, MAX_TITLE);
	}

	if(verify_image_data($image_data)) {
		/* convert from our custom 6bit base64 format to 24bit BMP, 
		   then back to base64 to be embedded in an img tag. */
		$bmp = image_data_to_bmp($image_data);
		$b64 = base64_encode($bmp);
		picture::insert($user, $title, $b64);
		$s->set_page($user);
	} else {
		request::push_get('err', 'bad image data');
	}
}

request::relocate('/');

?>
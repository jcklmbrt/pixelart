<?php


use mdl\picture;

 $s = new ctrl\session;    ?>
<?php if($s->logged_in()) { ?>
<?php $user = $s->get_local_user() ?>

<?php

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

?>

<div class="profile">
	<h1> <?= $user->username . "'s Artwork" ?> </h1>
	<?php foreach(picture::list_pictures($user) as $picture) { ?>
		<div class="picture">
			<h4> <?= $picture->title ?> </h4>
			<?php $bmp = image_data_to_bmp($picture->data); ?>
			<?php $src = 'data:bmp;base64,' . base64_encode($bmp); ?>
			<?= '<img src="'.$src.'">'; ?>
		</div>
	<?php } ?>
</div>


<?php } ?>
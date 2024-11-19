<?php


use mdl\picture;

 $s = new ctrl\session;    ?>
<?php if($s->logged_in()) { ?>
<?php $user = $s->get_local_user() ?>

<div class="profile">
	<h1> <?= $user->username . "'s Artwork" ?> </h1>
	<?php foreach(picture::list_pictures($user) as $picture) { ?>
		<div class="picture">
			<h4> <?= $picture->title ?> </h4>
			<?php $src = 'data:bmp;base64,' . $picture->data; ?>
			<?= '<img src="'.$src.'">'; ?>
		</div>
	<?php } ?>
</div>


<?php } ?>
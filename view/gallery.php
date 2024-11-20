<?php use mdl\picture; ?>
<?php $s = new ctrl\session; ?>
<?php if($s->logged_in()) { ?>
<?php $user = $s->get_local_user() ?>

<div class="gallery">
	<h1> <?= $user->username . "'s Artwork" ?> </h1>
	<?php foreach(picture::list_pictures($user) as $picture) { ?>
		<div class="picture">
			<h4> <?= $picture->title ?> by <?= $picture->user->username ?> </h4>
			<?php $src = 'data:bmp;base64,' . $picture->data; ?>
			<?= '<img src="'.$src.'">'; ?>
			<form action="ctrl/comment.php" method="POST">
				<label>Add Comment</label>
				<textarea name="msg"></textarea>
				<input name="pictureid" type="text" style="display: none;" value="<?= $picture->id ?>"></input>
				<input type="submit" value="Add Comment"></input>
			</form>
		</div>
	<?php } ?>
</div>


<?php } else { ?>
	<div class="gallery">
	<h1> Most Recent Artwork </h1>
	<?php foreach(picture::most_recent(10) as $picture) { ?>
		<div class="picture">
		<h4> <?= $picture->title ?> by <?= $picture->user->username ?> </h4>
		<?= '<img src="data:bmp;base64,'. $picture->data .'">'; ?>
		</div>
	<?php } ?>
	</div>
<?php } ?>
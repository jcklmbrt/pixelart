
<?php use mdl\user;    ?>
<?php use mdl\comment; ?>
<?php use mdl\picture; ?>
<?php use ctrl\request; ?>
<?php $s = new ctrl\session; ?>

<?php $user = request::get('user'); ?>
<?php $user = user::fetch($user);   ?>
<?php if(!is_null($user)) { ?>

<table class="gallery">
	<tr>
		<td><h1> <?= $user->username . "'s Artwork" ?> </h1></td>
	</tr>
	<?php foreach(picture::list_pictures($user) as $picture) { ?>
		<tr>
			<th colspan="2"> <?= $picture->title ?> by <?= $picture->user->username ?> </th>
		</tr>
		<tr>
			<td>
			<?= '<img src="data:image/bmp;base64,'. $picture->data .'">'; ?></img>
			</td>

			<td class="comment-section">
			<?php if($s->logged_in()) { ?>
				<form class="comment" action="ctrl/comment.php" method="POST">
					<textarea name="msg"></textarea>
					<input name="pictureid" type="text" style="display: none;" value="<?= $picture->id ?>"></input>
					<input type="submit" value="Add Comment"></input>
				</form>
			<?php } ?>

			<div class="comment-container">
			<?php foreach(comment::list_comments($picture) as $comment) { ?>
				<div class="comment">
					<?= $comment->user->username ?> says: <?= $comment->message ?>
				</div>
			<?php } ?>
			</div>
			</td>
		</tr>
	<?php } ?>
	</tr>
</table>

<?php } ?>
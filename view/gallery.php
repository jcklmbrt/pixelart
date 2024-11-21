<?php use mdl\comment; ?>
<?php use mdl\picture; ?>
<?php $s = new ctrl\session; ?>

<table class="gallery">
	<tr>
		<td><h1>Most Recent Artwork</h1></td>
	</tr>
	<?php foreach(picture::most_recent(10) as $picture) { ?>
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
					<small style="color:gray"><?= $comment->date->format('H:i j/m/y'); ?></small>
					<?= $comment->user->username ?> says: <?= $comment->message ?>
				</div>
			<?php } ?>
			</div>
			</td>
		</tr>
	<?php } ?>
	</tr>
</table>
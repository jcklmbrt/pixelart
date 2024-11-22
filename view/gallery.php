
<?php use mdl\user;    ?>
<?php use mdl\comment; ?>
<?php use mdl\picture; ?>
<?php use ctrl\request; ?>

<?php $s    = new ctrl\session; ?>
<?php $user = $s->page();       ?>
<?php $is_user = !is_null($user) && $user instanceof user;
if($is_user) { 
	$pictures = picture::list_pictures($user);
} else {
	$pictures = picture::most_recent(100);
}
	
	
?>

<table class="gallery">
	<tr>
		<?php if($is_user) { ?>
			<td colspan="2"><h1> <?= $user->username . "'s Artwork" ?> </h1></td>
		<?php } else { ?>
			<td colspan="2"><h1>Most Recent Artwork</h1></td>
		<?php } ?>
	</tr>
	<?php foreach($pictures as $picture) { ?>
		<?php $username = $picture->user->username; ?>
		<?php $userlink = "<a href='/?user=" . $username . "'>" . $username . '</a>'; ?>
		<tr>
			<th colspan="2"> 
				<?= $picture->title ?> by <?= $userlink ?> 
				<small><?= $picture->date->format('H:i j/m/y'); ?></small>
			</th>
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
			<?php $comments = comment::list_comments($picture) ?>
			<?php if(count($comments) == 0) { ?>
				<div class="comment">
					<small>&ast; no comments &ast;</small>
				</div>
			<?php } else foreach(comment::list_comments($picture) as $comment) { ?>
				<?php $username = $comment->user->username; ?>
				<?php $userlink = "<a href='/?user=" . $username . "'>" . $username . '</a>'; ?>
				<div class="comment">
					<small><?= $comment->date->format('H:i j/m/y'); ?></small>
					<?= $userlink . ': ' . $comment->message ?>
				</div>
			<?php } ?>
			</div>
			</td>
		</tr>
	<?php } ?>
	</tr>
</table>
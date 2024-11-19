
<?php $s = new ctrl\session; ?>

<?php if($s->logged_in()) { ?>
	<nav>
		<?= $s->get_local_user()->username; ?>
		<a href="ctrl/logout.php"><button>Logout</button></a>
	</nav>
<?php } ?>

<?php $s = new ctrl\session; ?>

<?php if($s->logged_in()) { ?>
	<nav>
		<?php echo $s->get_local_user()->username; ?>
		<a href="ctrl/login.php"><button>logout</button></a>
	</nav>
<?php } ?>
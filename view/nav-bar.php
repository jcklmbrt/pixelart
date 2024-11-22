
<?php use mdl\user;     ?>
<?php use ctrl\session; ?>
<?php $s = new session; ?>
<?php $page = $s->page() ?>

<nav>
<?php if($s->logged_in()) { ?>
	<?php $user = $s->get_local_user() ?>
	<?php if($page != 'new') { ?>
		<a href="/?page=new"><button>Create New Artwork</button></a>
	<?php } ?>

	<?php if($page != 'home') { ?>
		<a href="/?page=home"><button>Most Recent Artwork</button></a>
	<?php } ?>

	<?php if(!($page instanceof user && $user->id == $page->id)) { ?>
		<?= '<a href=/?user=' . $user->username . '>'; ?><button>My Artwork</button></a>
	<?php } ?>

<?php } else { ?>
	<?php if($s->page() != 'home') { ?>
		<a href="/?page=home"><button>Most Recent Artwork</button></a>
	<?php } else { ?>
		<a href="/?page=login"><button style="margin-right:0;">Login</button></a><span style="margin-left:0.5em;">to create artwork</span>
	<?php } ?>
<?php } ?>
<?php if($s->logged_in()) { ?>
	<a href="ctrl/logout.php"><button>Logout</button></a>
<?php } ?>
</nav>
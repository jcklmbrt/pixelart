
<?php

use ctrl\request;

 $s = new ctrl\session; ?>
<nav>
<?php if($s->logged_in()) { ?>
	<span class="outer">
		Hello 
		<?= $s->get_local_user()->username; ?>
		&colon;&rpar;
	</span>
	<?php if(request::get('page')) { ?>
		<a href="/"><button>Home</button></a>
	<?php } else { ?>
		<a href="/?page=new"><button>Create New Artwork</button></a>
	<?php } ?>
<?php } else { ?>
	<?php if(request::get('page')) { ?>
		<a href="/"><button>Most Recent Artwork</button></a>
	<?php } else { ?>
		<a href="/?page=login"><button style="margin-right:0;">Login</button></a><span style="margin-left:0.5em;">to create artwork</span>
	<?php } ?>
<?php } ?>
<?php if($s->logged_in()) { ?>
	<a href="ctrl/logout.php"><button>Logout</button></a>
<?php } ?>
</nav>
<form method="POST" action="/ctrl/login.php" class="login">
	<h1>Login</h1>
	<?php 
	if(isset($GLOBALS['err'])) { 
		echo $GLOBALS['err'];
	} 
	?>
	<label>Username:</label><input type="text"     name="username"></input>
	<label>Password:</label><input type="password" name="password"></input>
	<input type="submit"></input>
</form>
<?php use ctrl\request; ?>
<?php $err = request::get('login_error') ?> 

<form method="POST" action="/ctrl/login.php" class="login">
	<h1>Login</h1>
	<?php if(!is_null($err)) { ?>
		<div class="error"> <?php echo $err; ?> </div>
	<?php } ?>
	<label>Username:</label><input type="text"     name="username"></input>
	<label>Password:</label><input type="password" name="password"></input>
	<input type="submit"></input>
</form>
<?php use ctrl\request; ?>
<?php $err = request::get('register_error') ?> 

<form method="POST" action="/ctrl/register.php" class="login">
	<h1>Register</h1>
	<?php if(!is_null($err)) { ?>
		<p> <?php echo $err; ?> </p>
	<?php } ?>
	<label>Username:</label><input type="text"     name="username"></input>
	<label>Password:</label><input type="password" name="password"></input>
	<label>Re-enter Password:</label><input type="password" name="password2"></input>
	<input type="submit"></input>
</form>
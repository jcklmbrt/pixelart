<?php use ctrl\request; ?>
<?php $err = request::get('register_error'); ?>

<div class="form-container">
	<div class="title">
		<h1>Register</h1>
		<small>[<a href="/?page=login">already have an account?</a>]</small>
	</div>
	<form method="POST" action="/ctrl/register.php" class="login">
	<label>Username:</label><input type="text"     name="username"></input>
	<label>Password:</label><input type="password" name="password"></input>
	<label>Re-enter Password:</label><input type="password" name="password2"></input>
	<input type="submit" value="Submit"></input>
	<?php if(!is_null($err)) { ?>
		<div class="error"><?= $err; ?></div>
	<?php } ?>
	</form>
</div>
<?php use ctrl\session; ?>
<?php $s = new session; ?>

<div class="form-container">
	<div class="title">
		<h1>Register</h1>
		<small>[<a href="/?page=login">already have an account?</a>]</small>
	</div>
	<form method="POST" action="/ctrl/register.php">
		<label>Username:</label><input type="text"     name="username"></input>
		<label>Password:</label><input type="password" name="password"></input>
		<label>Re-enter Password:</label><input type="password" name="password2"></input>
		<input type="submit" value="Submit"></input>
		<?php while($err = $s->err_pop()) { ?>
			<div class="error"> &ast; <?= $err; ?> </div>
		<?php } ?>
	</form>
</div>
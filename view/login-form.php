<?php use ctrl\request; ?>
<?php $err = request::get('login_error') ?> 

<div class="form-container">
	<div class="title">
		<h1>Login</h1>
		<small>[<a href="/?page=register">create a new account</a>]</small>
	</div>
	<form method="POST" action="/ctrl/login.php">
		<label>Username:</label><input type="text"     name="username"></input>
		<label>Password:</label><input type="password" name="password"></input>
		<input type="submit" value="Submit"></input>
		<?php if(!is_null($err)) { ?>
			<div class="error"> <?= $err; ?> </div>
		<?php } ?>
	</form>
</div>
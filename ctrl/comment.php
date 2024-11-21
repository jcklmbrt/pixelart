<?php
set_include_path($_SERVER['DOCUMENT_ROOT']);
spl_autoload_extensions('.php');
spl_autoload_register();

use ctrl\request;
use ctrl\session;
use mdl\comment;
use mdl\picture;
use mdl\user;

$s = new session;

if(request::posted("msg", "pictureid")) {
	$msg       = htmlentities($_POST['msg']);
	$pictureid = intval($_POST['pictureid']);

	$user    = $s->get_local_user();
	$picture = picture::from_id($pictureid);

	comment::comment($user, $picture, $msg);
}

request::relocate("/");
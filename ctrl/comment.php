<?php
set_include_path($_SERVER['DOCUMENT_ROOT']);
spl_autoload_extensions('.php');
spl_autoload_register();

use ctrl\request;
use ctrl\session;
use mdl\comment;
use mdl\picture;

$s = new session;

const MAX_COMMENT = 256;

if(request::posted("msg", "pictureid")) {
	$msg       = htmlentities($_POST['msg']);
	$pictureid = intval($_POST['pictureid']);

	if(strlen($msg) > MAX_COMMENT) {
		$msg = substr($msg, 0, MAX_COMMENT);
	}

	$user    = $s->get_local_user();
	$picture = picture::from_id($pictureid);

	comment::comment($user, $picture, $msg);
}

request::relocate("/");
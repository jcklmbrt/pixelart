<?php
set_include_path($_SERVER['DOCUMENT_ROOT']);
spl_autoload_extensions('.php');
spl_autoload_register();

use ctrl\session;
use ctrl\request;

$s = new session;
$s->destroy();

request::relocate('/');

?>
<?php
namespace ctrl;

class request
{
	static function is_post() : bool 
	{
		return $_SERVER['REQUEST_METHOD'] == 'POST';
	}

	static function posted(string ...$items) : bool
	{
		if(!self::is_post()) {
			return false;
		}

		$r = true;
		foreach($items as $item) {
			$r = $r && isset($_POST[$item]);
		}
		return $r;
	}
}

?>
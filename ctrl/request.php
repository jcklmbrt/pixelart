<?php
namespace ctrl;

class request
{
	static private array $get_data = [];

	static function posted(string ...$items) : bool
	{
		if($_SERVER['REQUEST_METHOD'] != 'POST') {
			return false;
		}

		$r = true;
		foreach($items as $item) {
			$r = $r && isset($_POST[$item]);
		}
		return $r;
	}

	static function push_get(string $name, string $msg)
	{
		self::$get_data[$name] = $msg;
	}

	static function get(string $name) : ?string
	{
		if(!isset($_GET[$name])) {
			return null;
		} else {
			return $_GET[$name];
		}
	}

	static function relocate(string $path)
	{
		self::_relocate($path, self::$get_data);
	}

	private static function _relocate(string $path, array $get_data)
	{
		$full_path = $path;

		$first = true;
		foreach($get_data as $key => $val) {
			if($first) {
				$full_path .= '?';
				$first = false;
			} else {
				$full_path .= '&';
			}

			$full_path .= $key . "=" . $val;
		}

		header("Location: " . $full_path);
	}
}

?>
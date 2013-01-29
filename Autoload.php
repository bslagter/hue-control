<?php

namespace HueControl;

/**
 * @author Bert Slagter
 */
class Autoload
{
	public static function autoload($className)
	{
		// Parse namespaced classes to a valid path
		$namespaceParts = explode('\\', $className);

		if ($namespaceParts[0] != 'HueControl') {
			return;
		}

		$path = __DIR__ . '/' . implode('/', array_slice($namespaceParts, 1)) . '.php';

		if (file_exists($path)) {
			require_once($path);
		}
	}
}

<?php

namespace HueControl\Config;

/**
 * @author Bert Slagter
 */
class Environment
{
	public function check()
	{
		$errors = array();

		if (!extension_loaded('yaml')) {
			$errors[] = "Extension 'yaml' not loaded";
		}

		if (!extension_loaded('curl')) {
			$errors[] = "Extension 'curl' not loaded";
		}

		if (!empty($errors)) {
			echo
					'<h1>Could not start HueControl</h1>' .
					'<ul>';

			foreach ($errors as $str) {
				echo '<li>' . $str . '</li>';
			}

			echo '</ul>';
			exit;
		}
	}
}

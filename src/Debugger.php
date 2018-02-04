<?php


namespace Eagle;

use MilanKyncl\Debugbar\PhalconDebugbar;
use Phalcon\Debug as PhalconDebugger;


class Debugger {

	/**
	 * Set debugg mode
	 *
	 * @param $mode
	 */

	public static function setMode($mode) {

		$debugger = new PhalconDebugger();

		$debugbar = new PhalconDebugbar();
		$debugbar->setDebugMode($mode);

		if($debugbar->isDebugMode())
			$debugger->listen(true, true);

	}

	const ON = true;

	const OFF = false;

	/**
	 * Set header error
	 *
	 * @param $code
	 * @param $message
	 */

	public static function setHeaderError($code, $message) {

		header($_SERVER['SERVER_PROTOCOL'] . ' ' . $code . ' ' . $message, true, $code);

		die();
	}

}
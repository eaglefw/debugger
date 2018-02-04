<?php


namespace Eagle;

use Phalcon\Debug as PhalconDebug;
use MilanKyncl\Debugbar\PhalconDebugbar;

/**
 * Class Debug
 * @package Eagle
 */

class Debug extends PhalconDebug {

	// public $_uri = 'https://eaglefw.github.io/debugger/static/';


	public function __construct($mode = self::OFF) {

		$debugbar = new PhalconDebugbar();
		$debugbar->setDebugMode($mode);

		if($debugbar->isDebugMode())
			$this->listen(true, true);

	}

	const ON = true;

	const OFF = false;

}
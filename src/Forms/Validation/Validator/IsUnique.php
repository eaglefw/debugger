<?php

/**
 * MIT License
 * Copyright (c) 2016 Milan Kyncl
 * http://www.milankyncl.cz/
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Eagle\Forms\Validation\Validator;

use Phalcon\Validation;
use Phalcon\Validation\Validator;
use Phalcon\Validation\ValidatorInterface;
use Phalcon\Validation\Message;

class IsUnique extends Validator implements ValidatorInterface {

	/**
	 * IsUnique constructor.
	 *
	 * @param null|array $options - Validator Options
	 */

	public function __construct($options = null) {

		parent::__construct($options);

		$this->setOption('cancelOnFail', true);

	}


	/**
	 * Value validation
	 *
	 * @param  Validation $validation - validation object
	 * @param  string $attribute - validated attribute
	 * @return bool
	 */

	public function validate(Validation $validation, $attribute) {

		$model = $this->getOption( 'model' );
		$column = $this->getOption( 'column' );
		$ignoreId = $this->getOption('ignoreId');

		$value = $validation->getValue($attribute);

		if(is_null($ignoreId))
			$ignoreId = '-1';

		$row = $model::findFirst([
			$column . ' = :value: AND id != :id:',
			'bind' => [
				'value' => $value,
				'id' => $ignoreId
			]
		]);

		if(!$row)
			return true;

		$message = $this->getOption('message');

		if (!$message) {

			$message = '';
		}

		$validation->appendMessage(new Message($message, $attribute, 'IsInRow'));

		return false;

	}
}
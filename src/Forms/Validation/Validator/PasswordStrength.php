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


class PasswordStrength extends Validator implements ValidatorInterface {

	const MIN_VALID_SCORE = 2;

	/**
	 * Value validation
	 *
	 * @param   \Phalcon\Validation $validation - validation object
	 * @param   string $attribute - validated attribute
	 * @return  bool
	 */

	public function validate(Validation $validation, $attribute) {

		$allowEmpty = $this->getOption('allowEmpty');

		$value = $validation->getValue($attribute);

		if ($allowEmpty && ((is_scalar($value) && (string) $value === '') || is_null($value))) {
			return true;
		}

		$minScore = ($this->hasOption('minScore') ? $this->getOption('minScore') : self::MIN_VALID_SCORE);

		if (is_string($value) && $this->countScore($value) >= $minScore)
			return true;

		$message = ($this->hasOption('message') ? $this->getOption('message') : 'Password too weak');

		$validation->appendMessage(
			new Message($message, $attribute, 'PasswordStrengthValidator')
		);
		return false;
	}
	/**
	 * Calculates password strength score
	 *
	 * @param   string $value - password
	 * @return  int (1 = very weak, 2 = weak, 3 = medium, 4+ = strong)
	 */
	private function countScore($value)
	{
		$score = 0;
		$hasLower = preg_match('![a-z]!', $value);
		$hasUpper = preg_match('![A-Z]!', $value);
		$hasNumber = preg_match('![0-9]!', $value);
		if ($hasLower && $hasUpper) {
			++$score;
		}
		if (($hasNumber && $hasLower) || ($hasNumber && $hasUpper)) {
			++$score;
		}
		if (preg_match('![^0-9a-zA-Z]!', $value)) {
			++$score;
		}
		$length = mb_strlen($value);
		if ($length >= 16) {
			$score += 2;
		} elseif ($length >= 8) {
			++$score;
		} elseif ($length <= 4 && $score > 1) {
			--$score;
		} elseif ($length > 0 && $score === 0) {
			++$score;
		}
		return $score;
	}
}
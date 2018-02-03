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

use Phalcon\Tag;
use Phalcon\Filter;
use Phalcon\Validation\Message;
use Phalcon\Forms\Form as BaseForm;


class Form extends BaseForm {


	/**
     * Element error class
     *
	 * @var string $_elementErrorClass
	 */

    private $_elementErrorClass = 'has-error';

	/**
	 * UID Identificator
	 *
	 * @var string $_uid
	 */

    private $_uid;


    function initialize() {

        $this->assets->addInlineJs('$(function() { $(".' . $this->_elementErrorClass .'").focus(function() { $(this).removeClass("' . $this->_elementErrorClass . '"); })});');

    }

	/**
	 * Register component for method resolving
     *
     * @param string $methodName
	 */

	public function createComponent($methodName) {

		$this->_uid = $methodName;

		if($this->request->isPost('form_uid') && $this->request->getPost('form_uid') == $this->_uid) {

			if(!$this->isValid( $this->request->getPost() )){

				foreach($this->getMessages() as $el => $msg){

					$this->flashSession->error($msg);

				}

				$this->setDefaultsFromRequest($this->request);

			} else {

				if(!call_user_func([$this, $methodName], $this->getPostData())) {

				    $this->setDefaultsFromRequest($this->request);

                }

			}

		}

		return $this;

	}

	private function getPostData() {

		$data = Array();

		$filter = new Filter();

		foreach($this->request->getPost() as $name => $value) {

		    if($this->has($name)) {

                $filters = $this->get($name)->getFilters();

                if(!empty($filters)) {

                    foreach($filters as $filterName)
                        $value = $filter->sanitize($value, $filterName);

                }

		    }

			$data[$name] = $value;

		}

		return $data;

    }

	public function getIdentificator() {

		return Tag::hiddenField([ 'name' => 'form_uid', 'value' => $this->_uid, 'id' => 'form_uid' ]);

	}

	public function start($parameters = null) {

	    if(!isset($parameters['action']))
	        $parameters['action'] = '';

		return Tag::form($parameters);

	}

	public function end() {

		return $this->getIdentificator() . Tag::endForm();

	}

    /**
     * @param $name
     * @param bool $inline
     */


	public function renderDecorated($name) {
		?>
		<div class="form-group<?php if($this->hasMessagesFor($name)) echo ' has-error' ?>">
            <label style="margin-bottom: 5px;" class="control-label"><?= $this->getLabel($name) ?></label>
            <?= $this->render($name) ?>
            <?php $this->messages($name, '<span class="help-block text-danger m-b-none">', '</span>') ?>
		</div>
		<?php
	}

	/**
     * Render Form field
     *
     * @param string $name
     * @param null $attributes
     *
     * @return string
     */

	public function render($name, $attributes = null) {

	    $element = $this->get($name);

        if($this->hasMessagesFor($name)) {

            if(!isset($attributes['class'])) {

                $existingClass = $element->getAttribute('class');

                $attributes['class'] = '';

                if($existingClass) {

                    $attributes['class'] .= $existingClass;
                }
            }


            $attributes['class'] .= ' '. $this->_elementErrorClass;

        }

	    return parent::render($name, $attributes);

	}

	public function getLabel($name) {

	    return '<span' . ($this->hasMessagesFor($name) ? ' class="' . $this->_elementErrorClass . '"' : '') . '>' . parent::getLabel($name) . '</span>';

	}

	public function appendMessageTo($elementId, $message) {

	    $messages = $this->getMessagesFor($elementId);

	    $messages->appendMessage(new Message($message));

    }

	/**
	 * Set defaults from Request
	 * @param \Phalcon\Http\Request $request
	 */

	function setDefaultsFromRequest(\Phalcon\Http\Request $request) {

		foreach($request->getPost() as $field => $value) {

			if(is_scalar($value))
                Tag::setDefault($field, $value);

		}

		return $this;

	}

	/**
	 * Set defaults from Request
	 * @param \Phalcon\Http\Request $request
	 */

	function setDefaultsFromModel(Model $model) {

	    if(!isset($this->_uid))
	        throw new \Phalcon\Exception('Form _uid must be set before setting default values.');

		if($this->request->getPost('form_uid') != $this->_uid) {

			foreach($model->toArray() as $field => $value) {

			    if(is_scalar($value))
                    Tag::setDefault($field, $value);
			}

		}

		return $this;

	}

	/**
	 * Prints messages for a specific element
	 */

	public function messages($name, $before, $after) {

		if ($this->hasMessagesFor($name)) {
			foreach ($this->getMessagesFor($name) as $message) {
				echo $before . $message . $after . "\r\n";
			}
		}
	}

	/**
     * Set Error Class
     * @param $class
     */

	protected function setErrorClass($class) {

	    $this->_elementErrorClass = $class;

	}

	/**
	 * Utilities
	 * =========================================================
	 */

	public function error($error) {

		$this->flashSession->error($error);

	}

	public function success($message) {

		$this->flashSession->success($message);

	}

	/**
	 * Redirect method
	 *
	 * @param $url
	 */

	public function redirect($url) {

		$this->response->redirect($url);

	}

}
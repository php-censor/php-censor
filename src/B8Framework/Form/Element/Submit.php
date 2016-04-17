<?php

namespace b8\Form\Element;
use b8\Form\Element\Button,
	b8\View;

class Submit extends Button
{
	protected $_value = 'Submit';

	public function render($viewFile = null)
	{
		return parent::render(($viewFile ? $viewFile : 'Button'));
	}

	protected function _onPreRender(View &$view)
	{
		parent::_onPreRender($view);
		$view->type = 'submit';
	}
}
<?php

namespace b8\Form\Element;
use b8\View;

class Email extends Text
{
	public function render($viewFile = null)
	{
		return parent::render(($viewFile ? $viewFile : 'Text'));
	}

	protected function _onPreRender(View &$view)
	{
		parent::_onPreRender($view);
		$view->type = 'email';
	}
}
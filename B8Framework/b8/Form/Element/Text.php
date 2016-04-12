<?php

namespace b8\Form\Element;
use b8\Form\Input,
	b8\View;

class Text extends Input
{
	protected function _onPreRender(View &$view)
	{
		parent::_onPreRender($view);
		$view->type = 'text';
	}
}
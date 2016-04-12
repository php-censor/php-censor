<?php

namespace b8\Form\Element;
use b8\View,
	b8\Form\Input;

class Select extends Input
{
	protected $_options = array();

	public function setOptions(array $options)
	{
		$this->_options = $options;
	}

	protected function _onPreRender(View &$view)
	{
		parent::_onPreRender($view);
		$view->options = $this->_options;
	}
}
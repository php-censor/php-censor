<?php

namespace b8\Form\Element;

use b8\View;

class TextArea extends Text
{
    protected $_rows = 4;

    public function getRows()
    {
        return $this->_rows;
    }

    public function setRows($rows)
    {
        $this->_rows = $rows;
    }

    protected function onPreRender(View &$view)
    {
        parent::onPreRender($view);
        $view->rows = $this->getRows();
    }
}

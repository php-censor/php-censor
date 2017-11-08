<?php

namespace b8\Form\Element;

use b8\View;

class TextArea extends Text
{
    /**
     * @var integer
     */
    protected $_rows = 4;

    /**
     * @return integer
     */
    public function getRows()
    {
        return $this->_rows;
    }

    /**
     * @param integer $rows
     */
    public function setRows($rows)
    {
        $this->_rows = $rows;
    }

    /**
     * @param View $view
     */
    protected function onPreRender(View &$view)
    {
        parent::onPreRender($view);

        $view->rows = $this->getRows();
    }
}

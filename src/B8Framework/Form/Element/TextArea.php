<?php

namespace b8\Form\Element;

use PHPCensor\View;

class TextArea extends Text
{
    /**
     * @var integer
     */
    protected $rows = 4;

    /**
     * @return integer
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * @param integer $rows
     */
    public function setRows($rows)
    {
        $this->rows = $rows;
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

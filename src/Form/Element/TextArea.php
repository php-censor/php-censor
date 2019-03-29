<?php

namespace PHPCensor\Form\Element;

use PHPCensor\View;

class TextArea extends Text
{
    /**
     * @var int
     */
    protected $rows = 4;

    /**
     * @return int
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * @param int $rows
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

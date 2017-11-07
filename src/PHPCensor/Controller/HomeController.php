<?php

namespace PHPCensor\Controller;

use b8;
use PHPCensor\Helper\Lang;
use PHPCensor\Controller;

/**
 * Home Controller - Displays the Dashboard.
 */
class HomeController extends Controller
{
    /**
    * Display dashboard:
    */
    public function index()
    {
        $this->layout->title = Lang::get('dashboard');

        $widgets = [
            'left' => [],
            'right' => [],
        ];
        $widgets_config = b8\Config::getInstance()->get('php-censor.dashboard_widgets', [
            'all_projects' => [
                'side' => 'left',
            ],
            'last_builds' => [
                'side' => 'right',
            ],
        ]);
        foreach($widgets_config as $name => $params) {
            $side = (isset($params['side']) and ($params['side'] == 'right')) ? 'right' : 'left';
            $widgets[$side][$name] = $params;
        }

        $this->view->widgets = $widgets;

        return $this->view->render();
    }
}

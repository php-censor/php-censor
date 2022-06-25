<?php

declare(strict_types=1);

namespace PHPCensor;

use PHPCensor\Common\View\ViewFactoryInterface;
use PHPCensor\Common\View\ViewInterface;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class ViewFactory implements ViewFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createView(
        string $viewPath,
        ?string $viewExtension = null
    ): ViewInterface {
        return new View($viewPath, null, $viewExtension);
    }
}

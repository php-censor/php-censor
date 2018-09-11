<?php

namespace PHPCensor\ProcessControl;

/**
 * Construct an appropriate ProcessControl instance.
 *
 * @author Adirelle <adirelle@gmail.com>
 */
class Factory
{
    /**
     * ProcessControl singleton.
     *
     * @var ProcessControlInterface
     */
    protected static $instance = null;

    /**
     * Returns the ProcessControl singleton.
     *
     * @return ProcessControlInterface
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = static::createProcessControl();
        }
        return static::$instance;
    }

    /**
     * Create a ProcessControl depending on available extensions and the underlying OS.
     *
     * Check PosixProcessControl, WindowsProcessControl and UnixProcessControl, in that order.
     *
     * @return ProcessControlInterface
     *
     * @throws \Exception
     */
    public static function createProcessControl()
    {
        switch (true) {
            case PosixProcessControl::isAvailable():
                return new PosixProcessControl();
            case UnixProcessControl::isAvailable():
                return new UnixProcessControl();
        }

        throw new \Exception("No ProcessControl implementation available.");
    }
}

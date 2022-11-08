<?php

namespace PHPCensor\Plugin\Util;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class BitbucketNotifyPluginResult
{
    public const DEFAULT_PLUGIN_OUTPUT_FORMAT = "%s | %d\t=> %d\t%s";

    /** @var string $plugin */
    protected $plugin;

    /** @var int $left */
    protected $left;

    /** @var int $right */
    protected $right;

    /** @var string $outputFormat */
    protected $outputFormat = self::DEFAULT_PLUGIN_OUTPUT_FORMAT;

    public function __construct($plugin, $left, $right)
    {
        $this->plugin = $plugin;
        $this->left = $left;
        $this->right = $right;
    }

    public function getPlugin()
    {
        return $this->plugin;
    }

    public function setPlugin($plugin)
    {
        $this->plugin = $plugin;

        return $this;
    }

    public function getLeft()
    {
        return $this->left;
    }

    public function setLeft($left)
    {
        $this->left = $left;

        return $this;
    }

    public function getRight()
    {
        return $this->right;
    }

    public function setRight($right)
    {
        $this->right = $right;

        return $this;
    }

    public function isImproved()
    {
        return $this->right < $this->left;
    }

    public function isDegraded()
    {
        return $this->right > $this->left;
    }

    public function isUnchanged()
    {
        return $this->right === $this->left;
    }

    public function generateFormattedOutput($maxPluginNameLength)
    {
        return \trim(\sprintf(
            $this->outputFormat,
            \str_pad($this->plugin, $maxPluginNameLength),
            $this->left,
            $this->right,
            $this->generateComment()
        ));
    }

    public function generateTaskDescription()
    {
        if (!$this->isDegraded()) {
            return '';
        }

        return \sprintf(
            $this->getTaskDescriptionMessage(),
            $this->plugin,
            $this->left,
            $this->right
        );
    }

    protected function getTaskDescriptionMessage()
    {
        return 'pls fix %s because it has increased from %d to %d errors';
    }

    public function __toString()
    {
        return $this->plugin;
    }

    protected function generateComment()
    {
        if ($this->isDegraded()) {
            return '!!!!! o_O';
        }

        if ($this->isImproved()) {
            return 'great success!';
        }

        if ($this->left > 0 && $this->isUnchanged()) {
            return 'pls improve me :-(';
        }

        return '';
    }
}

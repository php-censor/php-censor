<?php

declare(strict_types=1);

namespace PHPCensor\Common;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class ParameterBag implements ParameterBagInterface, \IteratorAggregate, \Countable
{
    protected array $parameters;

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $key, $default = null)
    {
        if (\array_key_exists($key, $this->parameters)) {
            return $this->parameters[$key];
        }

        $parametersPart = $this->parameters;
        foreach (\explode('.', $key) as $keyPart) {
            if (\array_key_exists($keyPart, $parametersPart)) {
                $parametersPart = $parametersPart[$keyPart];
            } else {
                return $default;
            }
        }

        return $parametersPart;
    }

    public function has(string $key): bool
    {
        if (\array_key_exists($key, $this->parameters)) {
            return true;
        }

        $parametersPart = $this->parameters;
        foreach (\explode('.', $key) as $keyPart) {
            if (\array_key_exists($keyPart, $parametersPart)) {
                $parametersPart = $parametersPart[$keyPart];
            } else {
                return false;
            }
        }

        return true;
    }

    public function all(): array
    {
        return $this->parameters;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->parameters);
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return \count($this->parameters);
    }
}

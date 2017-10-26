<?php

namespace Mukharem\Deploy\Container;

use RuntimeException;

trait Container
{
    /**
     * @var mixed[]
     */
    private $instancesList = [];

    /**
     * @param mixed[] $instancesList
     * @param string $expectedClassName
     */
    public function setList(array $instancesList, $expectedClassName = '')
    {
        if (!empty($expectedClassName)) {
            array_walk(
                $instancesList,
                function ($instance) use ($expectedClassName) {
                    assert(is_a($instance, $expectedClassName));
                }
            );
        }

        $this->instancesList = $instancesList;
    }

    /**
     * @return mixed[]
     */
    public function getList()
    {
        return $this->instancesList;
    }

    /**
     * @param string $key
     * @return mixed
     * @throws RuntimeException
     */
    public function get($key)
    {
        if (!isset($this->instancesList[$key])) {
            throw new RuntimeException(sprintf("Element is not found by key '%s'", $key));
        }

        return $this->instancesList[$key];
    }

    /**
     * @param string $key
     * @param mixed $fallback
     * @return mixed
     */
    public function getOrElse($key, $fallback)
    {
        return (isset($this->instancesList[$key])) ? $this->instancesList[$key] : $fallback;
    }
}

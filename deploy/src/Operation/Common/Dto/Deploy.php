<?php

namespace Mukharem\Deploy\Operation\Common\Dto;

class Deploy implements InputInterface
{
    /**
     * @var int
     */
    private $runTimestamp;

    /**
     * @var string
     */
    private $version;

    /**
     * @param int $runTimestamp
     * @return $this
     */
    public function setRunTimestamp(int $runTimestamp)
    {
        $this->runTimestamp = $runTimestamp;

        return $this;
    }

    /**
     * @return int
     */
    public function getRunTimestamp()
    {
        return $this->runTimestamp;
    }

    /**
     * @param string $version
     * @return $this
     */
    public function setVersion(string $version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }
}

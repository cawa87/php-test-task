<?php

namespace Mukharem\Deploy\Bash\Dto;

class Result
{
    /**
     * @var int
     */
    private $returnCode;

    /**
     * @var string
     */
    private $stdOut;

    /**
     * @var string
     */
    private $stdError;

    /**
     * @return int|null
     */
    public function getReturnCode()
    {
        return $this->returnCode;
    }

    /**
     * @param int $returnCode
     * @return $this
     */
    public function setReturnCode(int $returnCode)
    {
        $this->returnCode = $returnCode;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getStdOut()
    {
        return $this->stdOut;
    }

    /**
     * @param string $stdOut
     * @return $this
     */
    public function setStdOut($stdOut)
    {
        $this->stdOut = $stdOut;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getStdError()
    {
        return $this->stdError;
    }

    /**
     * @param string $returnCode
     * @return $this
     */
    public function setStdError($stdError)
    {
        $this->stdError = $stdError;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->returnCode === 0;
    }
}

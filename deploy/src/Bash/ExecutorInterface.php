<?php

namespace Mukharem\Deploy\Bash;

use Mukharem\Deploy\Bash\Dto\Result;

interface ExecutorInterface
{
    /**
     * @param string $command
     * @return Result
     */
    public function execute(string $command): Result;
}

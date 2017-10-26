<?php

namespace Mukharem\Deploy\Bash;

use Mukharem\Deploy\Bash\Dto\Result;

final class LocalExecutor implements ExecutorInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute(string $command): Result
    {
        $stream = shell_exec($command);

        return
            (new Result())
                ->setReturnCode($this->obtainLastReturnCode())
                ->setStdOut((string)$stream)
            ;
    }

    /**
     * @return int
     */
    private function obtainLastReturnCode()
    {
         return (int)trim(shell_exec('echo $?'));
    }
}

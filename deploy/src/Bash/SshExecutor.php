<?php

namespace Mukharem\Deploy\Bash;

use Mukharem\Deploy\Bash\Dto\Result;
use RuntimeException;

final class SshExecutor implements ExecutorInterface
{
    /**
     * @var resource
     */
    private $connection;

    /**
     * @param $connection
     */
    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(string $command): Result
    {
        $stream = ssh2_exec($this->connection, $command);
        if ($stream === false) {
            throw new RuntimeException("Failed while executing the command: '$command'");
        }

        $errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);

        stream_set_blocking($errorStream, true);
        stream_set_blocking($stream, true);

        $stdOut = stream_get_contents($stream);
        $stdError = stream_get_contents($errorStream);

        fclose($errorStream);
        fclose($stream);

        return
            (new Result())
                ->setReturnCode($this->obtainLastReturnCode())
                ->setStdError((string)$stdError)
                ->setStdOut((string)$stdOut)
            ;
    }

    /**
     * @return int
     */
    private function obtainLastReturnCode()
    {
        $stream = ssh2_exec($this->connection, 'echo $?');
        if ($stream === false) {
            throw new RuntimeException("Failed fetching last return code");
        }

        stream_set_blocking($stream, true);

        $returnCode = stream_get_contents($stream);
        fclose($stream);

        return (int)$returnCode;
    }
}

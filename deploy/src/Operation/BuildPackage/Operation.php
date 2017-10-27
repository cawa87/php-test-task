<?php

namespace Mukharem\Deploy\Operation\BuildPackage;

use Mukharem\Deploy\Bash\Dto\Result;
use Mukharem\Deploy\Bash\ExecutorInterface;
use Mukharem\Deploy\Operation\Common\Dto\Deploy;
use Mukharem\Deploy\Operation\Common\Dto\InputInterface;
use Mukharem\Deploy\Operation\Common\Exception\OperationException;
use Mukharem\Deploy\Operation\Common\OperationInterface;

final class Operation implements OperationInterface
{
    /**
     * @var ExecutorInterface
     */
    private $bashExecutor;

    /**
     * @var string
     */
    private $packagePath;

    /**
     * @var string
     */
    private $vcsPath;

    /**
     * @param ExecutorInterface $bashExecutor
     * @param string $packagePath
     * @param string $vcsPath
     */
    public function __construct(ExecutorInterface $bashExecutor, string $packagePath, string $vcsPath)
    {
        $this->bashExecutor = $bashExecutor;
        $this->packagePath = $packagePath;
        $this->vcsPath = $vcsPath;
    }

    /**
     * @param InputInterface|Deploy $input
     * {@inheritdoc}
     */
    public function execute(InputInterface $input)
    {
        $versionDirName = $input->getVersion() . '__' . $input->getRunTimestamp();//todo move to separate class, has copy/past
        $versionDirPath = $this->packagePath . '/' . $versionDirName;

        $result = $this->bashExecutor->execute(
            "cd {$this->packagePath} && mkdir $versionDirName"
        );
        $this->checkResult($result);

        $result = $this->bashExecutor->execute(
            "cd $versionDirPath && git clone {$this->vcsPath} . && git checkout {$input->getVersion()}"
        );
        $this->checkResult($result);

        $result = $this->bashExecutor->execute("cd $versionDirPath && php composer.phar install");
        $this->checkResult($result);
    }

    /**
     * @param Result $result
     * @throws OperationException
     */
    private function checkResult(Result $result)
    {
        if (!$result->isSuccessful()) {
            throw new OperationException($result->getStdError(), $result->getReturnCode());
        }
    }
}

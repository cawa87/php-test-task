<?php

namespace Mukharem\Deploy\Command;

use Mukharem\Deploy\Operation\Common\Dto\Deploy as DeployDto;
use Mukharem\Deploy\Operation\Common\Exception\OperationException;
use Mukharem\Deploy\Operation\Common\OperationInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class Deploy extends Command
{
    const ARG_VERSION_NAME = 'version';

    /**
     * @var OperationInterface
     */
    private $deployOperation;

    /**
     * @param OperationInterface $deployOperation
     */
    public function __construct(OperationInterface $deployOperation)
    {
        $this->deployOperation = $deployOperation;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('deploy')
            ->addArgument(self::ARG_VERSION_NAME, InputArgument::REQUIRED, 'Which version do you want deploy?')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $inputDeployDto = (new DeployDto())
            ->setRunTimestamp(time())
            ->setVersion($input->getArgument(self::ARG_VERSION_NAME))
        ;

        try {
            $this->deployOperation->execute($inputDeployDto);
        } catch (OperationException $e) {
            $output->writeln("FAILED with code: '{$e->getCode()}', message: '{$e->getMessage()}'");
        }
    }
}

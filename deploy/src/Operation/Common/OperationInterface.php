<?php

namespace Mukharem\Deploy\Operation\Common;

use Mukharem\Deploy\Operation\Common\Dto\InputInterface;
use Mukharem\Deploy\Operation\Common\Exception\OperationException;

interface OperationInterface
{
    /**
     * @param InputInterface $input
     * @throws OperationException
     */
    public function execute(InputInterface $input);
}

<?php

namespace Mukharem\Deploy\Operation\Common;

use Mukharem\Deploy\Container\Container;
use Mukharem\Deploy\Operation\Common\Dto\InputInterface;

class OperationChain implements OperationInterface
{
    use Container;

    /**
     * @param OperationInterface[] $operations
     */
    public function __construct(array $operations)
    {
        $this->setList($operations, OperationInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input)
    {
        foreach ($this->getList() as $operation) {
            /** @var OperationInterface $operation */
            $operation->execute($input);
        }
    }
}

<?php

namespace PlentyConnector\Connector\CommandBus\Handler;

use PlentyConnector\Connector\CommandBus\Command\CommandInterface;

/**
 * Interface CommandHandlerInterface.
 */
interface CommandHandlerInterface
{
    /**
     * @param CommandInterface $command
     *
     * @return bool
     */
    public function supports($command);

    /**
     * @param CommandInterface $command
     */
    public function handle($command);
}
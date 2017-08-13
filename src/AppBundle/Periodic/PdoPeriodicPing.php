<?php

namespace AppBundle\Periodic;

use Gos\Bundle\WebSocketBundle\Periodic\PdoPeriodicPing as BasePeriodicPing;
use Psr\Log\LoggerInterface;

/**
 * Class PdoPeriodicPing
 * @package AppBundle\Periodic
 */
class PdoPeriodicPing extends BasePeriodicPing
{
    /**
     * PdoPeriodicPing constructor.
     * @param \PDO|null            $pdo
     * @param LoggerInterface|null $logger
     */
    public function __construct(\PDO $pdo = null, LoggerInterface $logger = null)
    {
        parent::__construct($pdo, $logger);
        $this->setTimeout(60);
    }
}

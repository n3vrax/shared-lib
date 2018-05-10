<?php
/**
 * Created by PhpStorm.
 * User: Tiberiu.Popa
 * Date: 10/05/2018
 * Time: 11:03
 */

declare(strict_types=1);

namespace n3vrax\SharedLib\Infrastructure\Doctrine;

use Doctrine\ORM\EntityManager;
use n3vrax\SharedLib\Application\TransactionSession;

/**
 * Class DoctrineTransactionSession
 *
 * @package n3vrax\ShareLib\Infrastructure\Doctrine
 */
class DoctrineTransactionSession implements TransactionSession
{
    /** @var EntityManager */
    private $em;

    /** @var int  */
    private $nested = 0;

    /**
     * DoctrineTransactionSession constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * start a transaction
     */
    public function beginTransaction()
    {
        $this->nested++;

        if ($this->nested > 1) {
            return;
        }

        $this->em->getConnection()->beginTransaction();
    }

    /**
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function commitTransaction()
    {
        $this->nested--;

        if (!$this->inTransaction() || $this->nested > 0) {
            return;
        }

        $this->em->getConnection()->commit();
    }

    /**
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function rollbackTransaction()
    {
        $this->nested = 0;

        if (!$this->inTransaction()) {
            return;
        }

        $this->em->getConnection()->rollback();
        $this->em->close();
    }

    /**
     * @return bool
     */
    private function inTransaction(): bool
    {
        return $this->em->getConnection()->isTransactionActive();
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: Tiberiu.Popa
 * Date: 10/05/2018
 * Time: 11:00
 */

declare(strict_types=1);

namespace n3vrax\SharedLib\Application;

/**
 * Interface TransactionSession
 *
 * @package n3vrax\ShareLib\Application
 */
interface TransactionSession
{
    public function beginTransaction();

    public function commitTransaction();

    public function rollbackTransaction();
}

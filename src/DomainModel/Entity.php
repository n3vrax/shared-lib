<?php
/**
 * Created by PhpStorm.
 * User: Tiberiu.Popa
 * Date: 10/05/2018
 * Time: 10:47
 */

declare(strict_types=1);

namespace n3vrax\ShareLib\DomainModel;

/**
 * Interface Entity
 *
 * @package n3vrax\ShareLib\DomainModel
 */
interface Entity
{
    /**
     * @param Entity $other
     *
     * @return bool
     */
    public function equals(Entity $other): bool;
}

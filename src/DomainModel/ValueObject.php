<?php
/**
 * Created by PhpStorm.
 * User: Tiberiu.Popa
 * Date: 10/05/2018
 * Time: 10:46
 */

declare(strict_types=1);

namespace n3vrax\ShareLib\DomainModel;

/**
 * Interface ValueObject
 *
 * @package n3vrax\ShareLib\DomainModel
 */
interface ValueObject
{
    /**
     * @param ValueObject $other
     *
     * @return bool
     */
    public function equals(ValueObject $other): bool;
}

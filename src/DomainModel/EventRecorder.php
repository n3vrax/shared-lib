<?php
/**
 * Created by PhpStorm.
 * User: Tiberiu.Popa
 * Date: 10/05/2018
 * Time: 10:52
 */

declare(strict_types=1);

namespace n3vrax\SharedLib\DomainModel;

use Prooph\Common\Messaging\DomainEvent;

/**
 * Interface EventRecorder
 *
 * @package n3vrax\ShareLib\DomainModel
 */
interface EventRecorder
{
    /**
     * @return DomainEvent[]
     */
    public function popRecordedEvents(): array;

    /**
     * @param DomainEvent $event
     *
     * @return mixed
     */
    public function recordThat(DomainEvent $event);

    /**
     * @return string
     */
    public function streamName(): string;
}

<?php
/**
 * Created by PhpStorm.
 * User: Tiberiu.Popa
 * Date: 10/05/2018
 * Time: 10:47
 */

declare(strict_types=1);

namespace n3vrax\ShareLib\DomainModel;

use Prooph\Common\Messaging\DomainEvent;

/**
 * Trait EventRecorderTrait
 *
 * @package n3vrax\ShareLib\DomainModel
 */
trait EventRecorderTrait
{
    /** @var string  */
    protected $streamName = '';

    /** @var array  */
    protected $recordedEvents = [];

    /**
     * @return DomainEvent[]
     */
    public function popRecordedEvents(): array
    {
        $pendingEvents = $this->recordedEvents;

        $this->recordedEvents = [];

        return $pendingEvents;
    }

    /**
     * @param DomainEvent $event
     */
    public function recordThat(DomainEvent $event)
    {
        $this->recordedEvents[] = $event;
    }

    /**
     * @return string
     */
    public function streamName(): string
    {
        return $this->streamName;
    }
}

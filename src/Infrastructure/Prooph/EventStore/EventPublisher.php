<?php
/**
 * Created by PhpStorm.
 * User: Tiberiu.Popa
 * Date: 10/05/2018
 * Time: 10:55
 */

declare(strict_types=1);

namespace n3vrax\ShareLib\Infrastructure\Prooph\EventStore;

use n3vrax\ShareLib\Infrastructure\Prooph\ServiceBus\TransactionalPlugin;
use Prooph\Common\Event\ActionEvent;
use Prooph\EventStore\ActionEventEmitterEventStore;
use Prooph\EventStore\Plugin\AbstractPlugin as AbstractEventStorePlugin;
use Prooph\ServiceBus\EventBus;
use Prooph\ServiceBus\MessageBus;
use Prooph\ServiceBus\Plugin\Plugin as MessageBusPlugin;

/**
 * Class EventPublisher
 *
 * @package n3vrax\ShareLib\Infrastructure\Prooph\EventStore
 */
class EventPublisher extends AbstractEventStorePlugin implements MessageBusPlugin
{
    /** @var EventBus */
    private $eventBus;

    /** @var array  */
    private $messageBusListeners = [];

    /** @var array  */
    private $cachedEventStreams = [];

    public function __construct(EventBus $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    public function detachFromMessageBus(MessageBus $messageBus): void
    {
        foreach ($this->messageBusListeners as $messageBusListener) {
            $messageBus->detach($messageBusListener);
        }

        $this->messageBusListeners = [];
    }

    public function attachToMessageBus(MessageBus $messageBus): void
    {
        $this->messageBusListeners[] = $messageBus->attach(
            MessageBus::EVENT_FINALIZE,
            function (ActionEvent $event): void {
                if ($event->getParam(MessageBus::EVENT_PARAM_EXCEPTION)) {
                    $this->cachedEventStreams = [];
                } else {
                    foreach ($this->cachedEventStreams as $stream) {
                        foreach ($stream as $recordedEvent) {
                            $this->eventBus->dispatch($recordedEvent);
                        }
                    }

                    $this->cachedEventStreams = [];
                }
            },
            TransactionalPlugin::PRIORITY_ROLLBACK_TRANSACTION - 500
        );
    }

    public function attachToEventStore(ActionEventEmitterEventStore $eventStore): void
    {
        $this->listenerHandlers[] = $eventStore->attach(
            ActionEventEmitterEventStore::EVENT_APPEND_TO,
            function (ActionEvent $event): void {
                $this->cachedEventStreams[] = $event->getParam('streamEvents', new \ArrayIterator());
            }
        );

        $this->listenerHandlers[] = $eventStore->attach(
            ActionEventEmitterEventStore::EVENT_CREATE,
            function (ActionEvent $event): void {
                if ($event->getParam('streamExistsAlready', false)) {
                    return;
                }

                $this->cachedEventStreams[] = $event->getParam('streamEvents', new \ArrayIterator());
            }
        );
    }
}

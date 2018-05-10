<?php
/**
 * Created by PhpStorm.
 * User: Tiberiu.Popa
 * Date: 10/05/2018
 * Time: 10:59
 */

declare(strict_types=1);

namespace n3vrax\SharedLib\Infrastructure\Prooph\ServiceBus;

use n3vrax\SharedLib\Application\TransactionSession;
use Prooph\Common\Event\ActionEvent;
use Prooph\ServiceBus\MessageBus;
use Prooph\ServiceBus\Plugin\AbstractPlugin;

/**
 * Class TransactionalPlugin
 *
 * @package n3vrax\ShareLib\Infrastructure\Prooph\ServiceBus
 */
class TransactionalPlugin extends AbstractPlugin
{
    const PRIORITY_OPEN_TRANSACTION = MessageBus::PRIORITY_INVOKE_HANDLER + 1000;
    const PRIORITY_COMMIT_TRANSACTION = 1000;
    const PRIORITY_ROLLBACK_TRANSACTION = 900;

    /** @var TransactionSession */
    private $transactionSession;

    public function __construct(TransactionSession $transactionSession)
    {
        $this->transactionSession = $transactionSession;
    }

    public function attachToMessageBus(MessageBus $messageBus): void
    {
        $this->listenerHandlers[] = $messageBus->attach(
            MessageBus::EVENT_DISPATCH,
            function (ActionEvent $event) {
                $this->transactionSession->beginTransaction();
            },
            self::PRIORITY_OPEN_TRANSACTION
        );

        $this->listenerHandlers[] = $messageBus->attach(
            MessageBus::EVENT_FINALIZE,
            function (ActionEvent $event) {
                if (!$event->getParam(MessageBus::EVENT_PARAM_EXCEPTION)) {
                    $this->transactionSession->commitTransaction();
                }
            },
            self::PRIORITY_COMMIT_TRANSACTION
        );

        $this->listenerHandlers[] = $messageBus->attach(
            MessageBus::EVENT_FINALIZE,
            function (ActionEvent $event) {
                if (!$event->getParam(MessageBus::EVENT_PARAM_EXCEPTION)) {
                    $this->transactionSession->rollbackTransaction();
                }
            },
            self::PRIORITY_ROLLBACK_TRANSACTION
        );
    }
}

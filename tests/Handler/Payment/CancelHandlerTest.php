<?php


namespace Handler\Payment;


use Broadway\CommandHandling\CommandHandlerInterface;
use Broadway\CommandHandling\Testing\CommandHandlerScenarioTestCase;
use Broadway\EventHandling\EventBusInterface;
use Broadway\EventStore\EventStoreInterface;
use Broadway\UuidGenerator\Rfc4122\Version4Generator;
use Broadway\UuidGenerator\UuidGeneratorInterface;
use Command\Payment\CancelCommand;
use Event\Payment\CancelledEvent;
use Event\Payment\CapturedEvent;
use Event\Payment\CreatedEvent;
use Event\Payment\RefundedEvent;
use Repository\PaymentRepository;
use RuntimeException;

class CancelHandlerTest extends CommandHandlerScenarioTestCase
{

    /** @var UuidGeneratorInterface */
    private $generator;

    public function setUp()
    {
        parent::setUp();
        $this->generator = new Version4Generator();
    }

    /**
     * Create a command handler for the given scenario test case.
     *
     * @param EventStoreInterface $eventStore
     * @param EventBusInterface $eventBus
     *
     * @return CommandHandlerInterface
     */
    protected function createCommandHandler(EventStoreInterface $eventStore, EventBusInterface $eventBus)
    {
        $repository = new PaymentRepository($eventStore, $eventBus);
        return new CancelHandler($repository);
    }

    /**
     *
     */
    public function testNewPaymentCanBeCancelled()
    {
        $paymentId = $this->generator->generate();
        $this->scenario
            ->withAggregateId($paymentId)
            ->given([new CreatedEvent($paymentId)])
            ->when(new CancelCommand($paymentId))
            ->then([new CancelledEvent($paymentId)]);
    }

    /**
     *
     */
    public function testCancelCancelledPaymentYieldsNoChange()
    {
        $paymentId = $this->generator->generate();
        $this->scenario
            ->withAggregateId($paymentId)
            ->given([new CreatedEvent($paymentId), new CancelledEvent($paymentId)])
            ->when(new CancelCommand($paymentId))
            ->then([]);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessageRegExp /Payment '[-A-Za-z0-9]+' in status 'confirmed' cannot be cancelled./
     */
    public function testConfirmedPaymentCannotBeCancelled()
    {
        $paymentId = $this->generator->generate();
        $this->scenario
            ->withAggregateId($paymentId)
            ->given([new CreatedEvent($paymentId), new CapturedEvent($paymentId)])
            ->when(new CancelCommand($paymentId));
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessageRegExp /Payment '[-A-Za-z0-9]+' in status 'refunded' cannot be cancelled./
     */
    public function testRefundedPaymentCannotBeCancelled()
    {
        $paymentId = $this->generator->generate();
        $this->scenario
            ->withAggregateId($paymentId)
            ->given([
                new CreatedEvent($paymentId),
                new CapturedEvent($paymentId),
                new RefundedEvent($paymentId)
            ])
            ->when(new CancelCommand($paymentId));
    }
}

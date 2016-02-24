<?php

namespace Handler\Payment;

use Broadway\CommandHandling\CommandHandlerInterface;
use Broadway\CommandHandling\Testing\CommandHandlerScenarioTestCase;
use Broadway\EventHandling\EventBusInterface;
use Broadway\EventStore\EventStoreInterface;
use Broadway\UuidGenerator\Rfc4122\Version4Generator;
use Broadway\UuidGenerator\UuidGeneratorInterface;
use Command\Payment\RefundCommand;
use Event\Payment\CapturedEvent;
use Event\Payment\CreatedEvent;
use Event\Payment\RefundedEvent;
use Repository\PaymentRepository;
use RuntimeException;

class RefundHandlerTest extends CommandHandlerScenarioTestCase
{
    /** @var UuidGeneratorInterface */
    private $generator;

    /**
     *
     */
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
        return new RefundHandler($repository);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessageRegExp /Payment '[-A-Za-z0-9]+' in status 'pending' cannot be refunded./
     */
    public function testNewPaymentCannotBeRefunded()
    {
        $paymentId = $this->generator->generate();
        $this->scenario
            ->withAggregateId($paymentId)
            ->given([new CreatedEvent($paymentId)])
            ->when(new RefundCommand($paymentId));
    }

    /**
     *
     */
    public function testConfirmedPaymentCanBeRefunded()
    {
        $paymentId = $this->generator->generate();
        $this->scenario
            ->withAggregateId($paymentId)
            ->given([new CreatedEvent($paymentId), new CapturedEvent($paymentId)])
            ->when(new RefundCommand($paymentId))
            ->then([new RefundedEvent($paymentId)]);
    }

    /**
     *
     */
    public function testRefundRefundedPaymentYieldsNoChange()
    {
        $paymentId = $this->generator->generate();
        $this->scenario
            ->withAggregateId($paymentId)
            ->given([
                new CreatedEvent($paymentId),
                new CapturedEvent($paymentId),
                new RefundedEvent($paymentId)
            ])
            ->when(new RefundCommand($paymentId))
            ->then([]);
    }
}

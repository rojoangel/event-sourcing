<?php

namespace Handler\Payment;

use Broadway\CommandHandling\CommandHandlerInterface;
use Broadway\CommandHandling\Testing\CommandHandlerScenarioTestCase;
use Broadway\EventHandling\EventBusInterface;
use Broadway\EventStore\EventStoreInterface;
use Broadway\UuidGenerator\Rfc4122\Version4Generator;
use Broadway\UuidGenerator\UuidGeneratorInterface;
use Command\Payment\CaptureCommand;
use Event\Payment\CapturedEvent;
use Event\Payment\CreatedEvent;
use Repository\PaymentRepository;

class CaptureHandlerTest extends CommandHandlerScenarioTestCase
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
        return new CaptureHandler($repository);
    }

    /**
     *
     */
    public function testNewPaymentCanBeCaptured()
    {
        $paymentId = $this->generator->generate();
        $this->scenario
            ->withAggregateId($paymentId)
            ->given([new CreatedEvent($paymentId)])
            ->when(new CaptureCommand($paymentId))
            ->then([new CapturedEvent($paymentId)]);
    }

    /**
     *
     */
    public function testCaptureCapturedPaymentYieldsNoChange()
    {
        $paymentId = $this->generator->generate();
        $this->scenario
            ->withAggregateId($paymentId)
            ->given([new CreatedEvent($paymentId), new CapturedEvent($paymentId)])
            ->when(new CaptureCommand($paymentId))
            ->then([]);
    }
}

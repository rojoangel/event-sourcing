<?php

namespace Handler\Payment;

use Broadway\CommandHandling\CommandHandlerInterface;
use Broadway\CommandHandling\Testing\CommandHandlerScenarioTestCase;
use Broadway\EventHandling\EventBusInterface;
use Broadway\EventStore\EventStoreInterface;
use Broadway\UuidGenerator\Rfc4122\Version4Generator;
use Broadway\UuidGenerator\UuidGeneratorInterface;
use Command\Payment\CreateCommand;
use Event\Payment\CreatedEvent;
use Repository\PaymentRepository;

class CreateHandlerTest extends CommandHandlerScenarioTestCase
{
    /** @var UuidGeneratorInterface  */
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
        return new CreateHandler($repository);
    }

    /**
     *
     */
    public function testNewPaymentTriggersCreatedEvent()
    {
        $paymentId = $this->generator->generate();

        $this->scenario
            ->withAggregateId($paymentId)
            ->given([])
            ->when(new CreateCommand($paymentId))
            ->then([new CreatedEvent($paymentId)]);
    }
}

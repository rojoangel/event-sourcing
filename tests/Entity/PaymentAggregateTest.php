<?php

namespace Entity;

use Broadway\EventSourcing\Testing\AggregateRootScenarioTestCase;
use Broadway\UuidGenerator\Rfc4122\Version4Generator;
use Broadway\UuidGenerator\UuidGeneratorInterface;
use Event\Payment\CreatedEvent;

class PaymentAggregateTest extends AggregateRootScenarioTestCase
{
    /** @var UuidGeneratorInterface */
    private $generator;

    public function setUp()
    {
        parent::setUp();
        $this->generator = new Version4Generator();
    }

    /**
     * Returns a string representing the aggregate root.
     *
     * @return string AggregateRoot
     */
    protected function getAggregateRootClass()
    {
        return PaymentAggregate::class;
    }

    public function testCreate()
    {
        $id = $this->generator->generate();

        $this->scenario
            ->when(function () use ($id) {
                return new PaymentAggregate($id);
            })
            ->then([new CreatedEvent($id)]);
    }
}

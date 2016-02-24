<?php


namespace Repository;


use Broadway\EventHandling\EventBusInterface;
use Broadway\EventSourcing\AggregateFactory\PublicConstructorAggregateFactory;
use Broadway\EventSourcing\EventSourcingRepository;
use Broadway\EventStore\EventStoreInterface;
use Entity\PaymentAggregate;

class PaymentRepository extends EventSourcingRepository
{
    /**
     * PaymentRepository constructor.
     * @param EventStoreInterface $eventStore
     * @param EventBusInterface $eventBus
     */
    public function __construct(
        EventStoreInterface $eventStore,
        EventBusInterface $eventBus
    ) {
        parent::__construct(
            $eventStore,
            $eventBus,
            PaymentAggregate::class,
            new PublicConstructorAggregateFactory()
        );
    }
}

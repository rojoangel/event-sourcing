<?php

namespace Handler\Payment;

use Broadway\CommandHandling\CommandHandler;
use Broadway\EventSourcing\EventSourcingRepository;
use Command\Payment;
use Entity\PaymentAggregate;

class CreateHandler extends CommandHandler
{
    /** @var EventSourcingRepository */
    private $repository;

    /**
     * CreateHandler constructor.
     *
     * @param EventSourcingRepository $repository
     */
    public function __construct(EventSourcingRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Payment\CreateCommand $command
     */
    public function handleCreateCommand(Payment\CreateCommand $command)
    {
        $payment = PaymentAggregate::create($command->getPaymentId());
        $this->repository->save($payment);
    }
}

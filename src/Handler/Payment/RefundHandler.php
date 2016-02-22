<?php

namespace Handler\Payment;

use Broadway\CommandHandling\CommandHandler;
use Broadway\EventSourcing\EventSourcingRepository;
use Command\Payment;
use Entity\PaymentAggregate;

class RefundHandler extends CommandHandler
{
    /** @var EventSourcingRepository */
    private $repository;

    /**
     * RefundHandler constructor.
     *
     * @param EventSourcingRepository $repository
     */
    public function __construct(EventSourcingRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Payment\RefundCommand $command
     */
    public function handleRefundCommand(Payment\RefundCommand $command)
    {
        /** @var PaymentAggregate $payment */
        $payment = $this->repository->load($command->getPaymentId());
        $payment->refund();
        $this->repository->save($payment);
    }
}

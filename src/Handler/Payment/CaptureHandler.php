<?php

namespace Handler\Payment;

use Broadway\CommandHandling\CommandHandler;
use Broadway\EventSourcing\EventSourcingRepository;
use Command\Payment;
use Entity\PaymentAggregate;

class CaptureHandler extends CommandHandler
{
    /** @var EventSourcingRepository */
    private $repository;

    /**
     * CaptureHandler constructor.
     *
     * @param EventSourcingRepository $repository
     */
    public function __construct(EventSourcingRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Payment\CaptureCommand $command
     */
    public function handleCaptureCommand(Payment\CaptureCommand $command)
    {
        /** @var PaymentAggregate $payment */
        $payment = $this->repository->load($command->getPaymentId());
        $payment->capture();
        $this->repository->save($payment);
    }
}

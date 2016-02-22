<?php

namespace Entity;

use Broadway\EventSourcing\EventSourcedAggregateRoot;
use Event\Payment;

class PaymentAggregate extends EventSourcedAggregateRoot
{
    /** @var string */
    private $paymentId;

    /**
     * @return string
     */
    public function getAggregateRootId()
    {
        return $this->paymentId;
    }

    /**
     * PaymentAggregate constructor.
     *
     * @param string $paymentId
     */
    public function __construct($paymentId)
    {
        $this->paymentId = $paymentId;
        $this->apply(new Payment\CreatedEvent($this->paymentId));
    }

    /**
     *
     */
    public function capture()
    {
        $this->apply(new Payment\CapturedEvent($this->paymentId));
    }

    /**
     *
     */
    public function refund()
    {
        $this->apply(new Payment\RefundedEvent($this->paymentId));
    }

    /**
     *
     */
    public function cancel()
    {
        $this->apply(new Payment\CancelledEvent($this->paymentId));
    }
}

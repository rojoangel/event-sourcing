<?php

namespace Entity;

use Broadway\EventSourcing\EventSourcedAggregateRoot;
use Event\Payment;

class Payment extends EventSourcedAggregateRoot
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
     * @param string $paymentId
     * @return \Entity\Payment
     */
    public static function create($paymentId)
    {
        $payment = new Payment();
        $payment->apply(new Payment\CreatedEvent($paymentId));
        return $payment;
    }

    /**
     * @param $paymentId
     */
    public function capture($paymentId)
    {
        $this->apply(new Payment\CapturedEvent($paymentId));
    }

    /**
     * @param $paymentId
     */
    public function refund($paymentId)
    {
        $this->apply(new Payment\RefundedEvent($paymentId));
    }

    /**
     * @param $paymentId
     */
    public function cancel($paymentId)
    {
        $this->apply(new Payment\CancelledEvent($paymentId));
    }
}

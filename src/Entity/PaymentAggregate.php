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
     * @param string $paymentId
     *
     * @return PaymentAggregate
     */
    public static function create($paymentId)
    {
        $payment = new self();
        $payment->apply(new Payment\CreatedEvent($paymentId));

        return $payment;
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

    /**
     * @param Payment\CreatedEvent $event
     */
    protected function applyCreatedEvent(Payment\CreatedEvent $event) {
        $this->paymentId = $event->getPaymentId();
    }
}

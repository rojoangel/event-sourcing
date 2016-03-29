<?php

namespace Entity;

use Broadway\EventSourcing\EventSourcedAggregateRoot;
use Event\Payment;
use RuntimeException;
use SM\StateMachine\StateMachine;
use StateMachine\PaymentStateMachine;

class PaymentAggregate extends EventSourcedAggregateRoot
{

    /** @var string */
    private $state;

    /** @var string */
    private $paymentId;

    /** @var StateMachine */
    private $stateMachine;

    public function __construct()
    {
        $this->state = 'checkout';
        $this->stateMachine = new PaymentStateMachine($this);
    }

    /**
     * @return string
     */
    public function getAggregateRootId()
    {
        return $this->paymentId;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState($state)
    {
        $this->state = $state;
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
        if ($this->stateMachine->getState() !== 'confirmed') {
            $this->apply(new Payment\CapturedEvent($this->paymentId));
        }
    }

    /**
     *
     */
    public function refund()
    {
        if ($this->stateMachine->getState() == 'refunded') {
            return;
        }

        if (!$this->stateMachine->can('refund')) {
            throw new RuntimeException(
                sprintf(
                    'Payment \'%s\' in status \'%s\' cannot be refunded.',
                    $this->paymentId,
                    $this->stateMachine->getState()
                ));
        }

        $this->apply(new Payment\RefundedEvent($this->paymentId));
    }

    /**
     *
     */
    public function cancel()
    {
        if ($this->stateMachine->getState() == 'cancelled') {
            return;
        }

        if (!$this->stateMachine->can('cancel')) {
            throw new RuntimeException(
                sprintf(
                    'Payment \'%s\' in status \'%s\' cannot be cancelled.',
                    $this->paymentId,
                    $this->stateMachine->getState()
                ));
        }

        $this->apply(new Payment\CancelledEvent($this->paymentId));
    }


    /**
     * @param Payment\CreatedEvent $event
     * @throws \SM\SMException
     */
    protected function applyCreatedEvent(Payment\CreatedEvent $event)
    {
        $this->paymentId = $event->getPaymentId();
        $this->stateMachine->apply('create');
    }

    /**
     * @throws \SM\SMException
     */
    protected function applyCapturedEvent()
    {
        $this->stateMachine->apply('capture');
    }

    /**
     * @throws \SM\SMException
     */
    protected function applyRefundedEvent()
    {
        $this->stateMachine->apply('refund');
    }

    /**
     * @throws \SM\SMException
     */
    protected function applyCancelledEvent()
    {
        $this->stateMachine->apply('cancel');
    }
}
<?php

namespace Entity;

use Broadway\EventSourcing\EventSourcedAggregateRoot;
use Event\Payment;
use SM\StateMachine\StateMachine;

class PaymentAggregate extends EventSourcedAggregateRoot
{

    /** @var array */
    private $config = [
        'graph' => 'paymentGraph',
        'property_path' => 'state',
        'states' => [
            'checkout',
            'pending',
            'confirmed',
            'refunded',
            'cancelled'
        ],
        'transitions' => [
            'create' => [
                'from' => ['checkout', 'pending'],
                'to'   => 'pending'
            ],
            'capture' => [
                'from' => ['checkout', 'pending'],
                'to'   => 'confirmed'
            ],
            'refund' => [
                'from' => ['checkout', 'pending'],
                'to'   => 'refunded'
            ],
            'cancel' => [
                'from' => ['confirmed'],
                'to'   => 'cancelled'
            ]
        ],
    ];

    /** @var string */
    private $state = 'checkout';

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
        $stateMachine = new StateMachine($this, $this->config);
        if ($stateMachine->getState() !== 'confirmed') {
            $this->apply(new Payment\CapturedEvent($this->paymentId));
        }
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
     * @throws \SM\SMException
     */
    protected function applyCreatedEvent(Payment\CreatedEvent $event)
    {
        $this->paymentId = $event->getPaymentId();
        $stateMachine = new StateMachine($this, $this->config);
        $stateMachine->apply('create');
    }

    /**
     * @param Payment\CapturedEvent $event
     * @throws \SM\SMException
     */
    protected function applyCapturedEvent(Payment\CapturedEvent $event)
    {
        $stateMachine = new StateMachine($this, $this->config);
        $stateMachine->apply('capture');
    }
}

<?php

namespace Entity;

use Broadway\EventSourcing\EventSourcedAggregateRoot;
use Event\Payment;
use RuntimeException;
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
                'from' => ['confirmed'],
                'to'   => 'refunded'
            ],
            'cancel' => [
                'from' => ['pending'],
                'to'   => 'cancelled'
            ]
        ],
    ];

    /** @var string */
    private $state;

    /** @var string */
    private $paymentId;

    public function __construct()
    {
        $this->state = 'checkout';
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
        $stateMachine = new StateMachine($this, $this->config);

        if ($stateMachine->getState() == 'refunded') {
            return;
        }

        if (!$stateMachine->can('refund')) {
            throw new RuntimeException(
                sprintf(
                    'Payment \'%s\' in status \'%s\' cannot be refunded.',
                    $this->paymentId,
                    $stateMachine->getState()
                ));
        }

        $this->apply(new Payment\RefundedEvent($this->paymentId));
    }

    /**
     *
     */
    public function cancel()
    {
        $stateMachine = new StateMachine($this, $this->config);

        if ($stateMachine->getState() == 'cancelled') {
            return;
        }

        if (!$stateMachine->can('cancel')) {
            throw new RuntimeException(
                sprintf(
                    'Payment \'%s\' in status \'%s\' cannot be cancelled.',
                    $this->paymentId,
                    $stateMachine->getState()
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
        $stateMachine = new StateMachine($this, $this->config);
        $stateMachine->apply('create');
    }

    /**
     * @throws \SM\SMException
     */
    protected function applyCapturedEvent()
    {
        $stateMachine = new StateMachine($this, $this->config);
        $stateMachine->apply('capture');
    }

    /**
     * @throws \SM\SMException
     */
    protected function applyRefundedEvent()
    {
        $stateMachine = new StateMachine($this, $this->config);
        $stateMachine->apply('refund');
    }

    /**
     * @throws \SM\SMException
     */
    protected function applyCancelledEvent()
    {
        $stateMachine = new StateMachine($this, $this->config);
        $stateMachine->apply('cancel');
    }
}
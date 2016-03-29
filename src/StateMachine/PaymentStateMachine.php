<?php

namespace StateMachine;

use SM\Callback\CallbackFactoryInterface;
use SM\SMException;
use SM\StateMachine\StateMachine;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PaymentStateMachine extends StateMachine
{
    /** @var array */
    private $paymentConfig = [
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

    /**
     * @param object                   $object          Underlying object for the state machine
     * @param EventDispatcherInterface $dispatcher      EventDispatcher or null not to dispatch events
     * @param CallbackFactoryInterface $callbackFactory CallbackFactory or null to use the default one
     *
     * @throws SMException If object doesn't have configured property path for state
     */
    public function __construct(
        $object,
        EventDispatcherInterface $dispatcher = null,
        CallbackFactoryInterface $callbackFactory = null)
    {
        parent::__construct($object, $this->paymentConfig, $dispatcher, $callbackFactory);
    }
}

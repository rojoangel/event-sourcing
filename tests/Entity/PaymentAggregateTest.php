<?php

namespace Entity;

use Broadway\EventSourcing\Testing\AggregateRootScenarioTestCase;
use Broadway\UuidGenerator\Rfc4122\Version4Generator;
use Broadway\UuidGenerator\UuidGeneratorInterface;
use Event\Payment\CancelledEvent;
use Event\Payment\CapturedEvent;
use Event\Payment\CreatedEvent;
use Event\Payment\RefundedEvent;
use RuntimeException;

class PaymentAggregateTest extends AggregateRootScenarioTestCase
{
    /** @var UuidGeneratorInterface */
    private $generator;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        $this->generator = new Version4Generator();
    }

    /**
     * Returns a string representing the aggregate root.
     *
     * @return string AggregateRoot
     */
    protected function getAggregateRootClass()
    {
        return PaymentAggregate::class;
    }

    /**
     *
     */
    public function testNewPaymentTriggersCreatedEvent()
    {
        $paymentId = $this->generator->generate();

        $this->scenario
            ->when(function () use ($paymentId) {
                return PaymentAggregate::create($paymentId);
            })
            ->then([new CreatedEvent($paymentId)]);
    }

    /**
     *
     */
    public function testNewPaymentCanBeCaptured()
    {
        $paymentId = $this->generator->generate();
        $this->scenario
            ->withAggregateId($paymentId)
            ->given([new CreatedEvent($paymentId)])
            ->when(function (PaymentAggregate $aggregate) {
                $aggregate->capture();
            })
            ->then([new CapturedEvent($paymentId)]);
    }

    /**
     *
     */
    public function testCaptureCapturedPaymentYieldsNoChange()
    {
        $paymentId = $this->generator->generate();
        $this->scenario
            ->withAggregateId($paymentId)
            ->given([new CreatedEvent($paymentId), new CapturedEvent($paymentId)])
            ->when(function (PaymentAggregate $aggregate) {
                $aggregate->capture();
            })
            ->then([]);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessageRegExp /Payment '[-A-Za-z0-9]+' in status 'pending' cannot be refunded./
     */
    public function testNewPaymentCannotBeRefunded()
    {
        $paymentId = $this->generator->generate();
        $this->scenario
            ->withAggregateId($paymentId)
            ->given([new CreatedEvent($paymentId)])
            ->when(function (PaymentAggregate $aggregate) {
                $aggregate->refund();
            });
    }

    /**
     *
     */
    public function testConfirmedPaymentCanBeRefunded()
    {
        $paymentId = $this->generator->generate();
        $this->scenario
            ->withAggregateId($paymentId)
            ->given([new CreatedEvent($paymentId), new CapturedEvent($paymentId)])
            ->when(function (PaymentAggregate $aggregate) {
                $aggregate->refund();
            })
            ->then([new RefundedEvent($paymentId)]);
    }

    /**
     *
     */
    public function testRefundRefundedPaymentYieldsNoChange()
    {
        $paymentId = $this->generator->generate();
        $this->scenario
            ->withAggregateId($paymentId)
            ->given([
                new CreatedEvent($paymentId),
                new CapturedEvent($paymentId),
                new RefundedEvent($paymentId)
            ])
            ->when(function (PaymentAggregate $aggregate) {
                $aggregate->refund();
            })
            ->then([]);
    }

    /**
     *
     */
    public function testNewPaymentCanBeCancelled()
    {
        $paymentId = $this->generator->generate();
        $this->scenario
            ->withAggregateId($paymentId)
            ->given([new CreatedEvent($paymentId)])
            ->when(function (PaymentAggregate $aggregate) {
                $aggregate->cancel();
            })
            ->then([new CancelledEvent($paymentId)]);
    }

    /**
     *
     */
    public function testCancelCancelledPaymentYieldsNoChange()
    {
        $paymentId = $this->generator->generate();
        $this->scenario
            ->withAggregateId($paymentId)
            ->given([new CreatedEvent($paymentId), new CancelledEvent($paymentId)])
            ->when(function (PaymentAggregate $aggregate) {
                $aggregate->cancel();
            })
            ->then([]);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessageRegExp /Payment '[-A-Za-z0-9]+' in status 'confirmed' cannot be cancelled./
     */
    public function testConfirmedPaymentCannotBeCancelled()
    {
        $paymentId = $this->generator->generate();
        $this->scenario
            ->withAggregateId($paymentId)
            ->given([new CreatedEvent($paymentId), new CapturedEvent($paymentId)])
            ->when(function (PaymentAggregate $aggregate) {
                $aggregate->cancel();
            });
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessageRegExp /Payment '[-A-Za-z0-9]+' in status 'refunded' cannot be cancelled./
     */
    public function testRefundedPaymentCannotBeCancelled()
    {
        $paymentId = $this->generator->generate();
        $this->scenario
            ->withAggregateId($paymentId)
            ->given([
                new CreatedEvent($paymentId),
                new CapturedEvent($paymentId),
                new RefundedEvent($paymentId)
            ])
            ->when(function (PaymentAggregate $aggregate) {
                $aggregate->cancel();
            });
    }
}

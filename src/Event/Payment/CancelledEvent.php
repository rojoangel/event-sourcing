<?php

namespace Event\Payment;

class CancelledEvent
{
    /** @var string */
    private $paymentId;

    /**
     * CancelledEvent constructor.
     *
     * @param $paymentId
     */
    public function __construct($paymentId)
    {
        $this->paymentId = $paymentId;
    }

    /**
     * @return mixed
     */
    public function getPaymentId()
    {
        return $this->paymentId;
    }
}

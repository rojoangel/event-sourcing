<?php

namespace Event\Payment;

class CreatedEvent
{
    /** @var string */
    private $paymentId;

    /**
     * CreatedEvent constructor.
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

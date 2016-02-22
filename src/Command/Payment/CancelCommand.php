<?php

namespace Command\Payment;

class CancelCommand
{
    /** @var string */
    private $paymentId;

    /**
     * CancelCommand constructor.
     *
     * @param string $paymentId
     */
    public function __construct($paymentId)
    {
        $this->paymentId = $paymentId;
    }

    /**
     * @return string
     */
    public function getPaymentId()
    {
        return $this->paymentId;
    }
}

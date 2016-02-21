<?php

namespace Command\Payment;

class RefundCommand
{
    /** @var string */
    private $paymentId;

    /**
     * RefundCommand constructor.
     *
     * @param string $paymentId
     */
    public function __construct($paymentId)
    {
        $this->paymentId = $paymentId;
    }
}

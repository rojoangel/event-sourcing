<?php

namespace Command\Payment;

class CaptureCommand
{
    /** @var string */
    private $paymentId;

    /**
     * CaptureCommand constructor.
     *
     * @param string $paymentId
     */
    public function __construct($paymentId)
    {
        $this->paymentId = $paymentId;
    }
}

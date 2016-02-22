<?php

namespace Command\Payment;

class CreateCommand
{
    /** @var  string */
    private $paymentId;

    /**
     * CreateCommand constructor.
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

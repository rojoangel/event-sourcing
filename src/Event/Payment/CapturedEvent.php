<?php


namespace Event\Payment;


class CapturedEvent
{

    /** @var string */
    private $paymentId;

    /**
     * CapturedEvent constructor.
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

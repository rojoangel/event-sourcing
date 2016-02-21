<?php


namespace Event\Payment;


class RefundedEvent
{
    /** @var string */
    private $paymentId;

    /**
     * RefundedEvent constructor.
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

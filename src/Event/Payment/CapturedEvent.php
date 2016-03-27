<?php

namespace Event\Payment;

use Broadway\Serializer\SerializableInterface;

class CapturedEvent implements SerializableInterface
{
    /** @var string */
    private $paymentId;

    /**
     * CapturedEvent constructor.
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

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self($data['paymentId']);
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'paymentId' => $this->paymentId
        ];
    }
}

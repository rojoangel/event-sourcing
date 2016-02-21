<?php

namespace Entity;

use Broadway\EventSourcing\EventSourcedAggregateRoot;

class Payment extends EventSourcedAggregateRoot
{
    /** @var string */
    private $paymentId;

    /**
     * @return string
     */
    public function getAggregateRootId()
    {
        return $this->paymentId;
    }
}

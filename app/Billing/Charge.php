<?php

namespace App\Billing;

class Charge
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function cardLastFour(): string
    {
        return $this->data['card_last_four'];
    }

    /**
     * @return int
     */
    public function amount(): int
    {
        return $this->data['amount'];
    }
}

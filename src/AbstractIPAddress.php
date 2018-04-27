<?php

namespace hschulz\Network;

/**
 * This class is the parent class of all ip address classes.
 */
abstract class AbstractIPAddress
{

    /**
     * Contains the internal representation of the ip address.
     * @var string
     */
    protected $value = '';

    /**
     *
     * @return bool
     */
    abstract public function isValid(): bool;

    /**
     *
     * @param string $value
     * @return void
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }
}

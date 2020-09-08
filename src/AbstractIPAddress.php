<?php

declare(strict_types=1);

namespace hschulz\Network;

use Hschulz\Network\Validatable;

/**
 * This class is the parent class of all ip address classes.
 */
abstract class AbstractIPAddress implements Validatable
{
    /**
     * Contains the internal representation of the ip address.
     * @var string
     */
    protected $value = '';

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

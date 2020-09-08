<?php

declare(strict_types=1);

namespace hschulz\Network;

use Hschulz\Network\Validatable;

/**
 * Description of Port
 */
class Port implements Validatable
{
    /**
     * Name for the well known range.
     * @var string
     */
    const NAME_WELL_KNOWN = 'WELL_KNOWN';

    /**
     * Name for the registered range.
     * @var string
     */
    const NAME_REGISTERED = 'REGISTERED';

    /**
     * Name for the private or public range.
     * @var string
     */
    const NAME_PRIVATE = 'PRIVATE';

    /**
     * Name for the invalid range.
     * @var string
     */
    const NAME_INVALID = 'INVALID';

    /**
     * Starting value for the well known range.
     * @var int
     */
    const RANGE_WELL_KNOWN_START = 0;

    /**
     * Ending value for the well known range.
     * @var int
     */
    const RANGE_WELL_KNOWN_END = 1023;

    /**
     * Starting value for the registered range.
     * @var int
     */
    const RANGE_REGISTERED_START = 1024;

    /**
     * Ending value for the registered range.
     * @var int
     */
    const RANGE_REGISTERED_END = 49151;

    /**
     * Starting value for the private or public range.
     * @var int
     */
    const RANGE_PRIVATE_START = 49152;

    /**
     * Ending value for the private or public range.
     * @var int
     */
    const RANGE_PRIVATE_END = 65535;

    /**
     * The actual port number.
     * @var int
     */
    private $number = self::RANGE_WELL_KNOWN_START;

    /**
     * Identifier for the port-range the port is in.
     * @var string
     */
    private $type = self::NAME_INVALID;

    /**
     * Creates a new port from a given value.
     *
     * @param int $port The port value
     */
    public function __construct(int $port)
    {
        $this->number = $port;
        $this->type = self::NAME_INVALID;
        $this->parse();
    }

    /**
     * Converts the port to a string representation.
     *
     * @return string The port number
     */
    public function __toString(): string
    {
        return (string) $this->number;
    }

    /**
     *
     */
    protected function parse(): void
    {
        switch ($this->number) {

            case $this->number >= self::RANGE_WELL_KNOWN_START:
            case $this->number <= self::RANGE_WELL_KNOWN_END:
                $this->type = self::NAME_WELL_KNOWN;
                break;

            case $this->number >= self::RANGE_REGISTERED_START:
            case $this->number <= self::RANGE_REGISTERED_END:
                $this->type = self::NAME_REGISTERED;
                break;

            case $this->number >= self::RANGE_PRIVATE_START:
            case $this->number <= self::RANGE_PRIVATE_END:
                $this->type = self::NAME_PRIVATE;
                break;

            default:
                $this->type = self::NAME_INVALID;
        }
    }

    /**
     *
     * @return bool True if the port value is in between the allowed range.
     */
    public function isValid(): bool
    {
        return ($this->type !== self::NAME_INVALID) ? true : false;
    }

    /**
     *
     * @return string The ports type.
     */
    public function getType(): string
    {
        return $this->type;
    }
}

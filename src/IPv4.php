<?php

declare(strict_types=1);

namespace Hschulz\Network;

use function bindec;
use function count;
use function decbin;
use function explode;
use function implode;
use function str_pad;
use const STR_PAD_LEFT;

/**
 *
 */
class IPv4 extends AbstractIPAddress
{
    /**
     * A value representing an invalid notation.
     * @var int
     */
    public const  NOTATION_INVALID = -1;

    /**
     * A value representing the dot decimal notation.
     * @example 127.0.0.1
     * @var int
     */
    public const NOTATION_DOT_DECIMAL = 0;

    /**
     * A value representing a binary notation.
     * @example 01111111.00000000.00000000.00000001
     * @var int
     */
    public const NOTATION_BINARY = 1;

    /**
     * A value representing the short CIDR notation.
     * @example 127.0.0.1/32
     * @var int
     */
    public const NOTATION_CIDR_SHORT = 2;

    /**
     * A value representing the long CIDR notation.
     * @example 127.0.0.1/255.255.255.255
     * @var int
     */
    public const NOTATION_CIDR_LONG = 3;

    /**
     * A value representing the binary CIDR notation.
     * @example 01111111.00000000.00000000.00000001/11111111.11111111.11111111.11111111
     * @var int
     */
    public const NOTATION_CIDR_BINARY = 4;

    /**
     * The minimal value a segment of an IPv4 address can have.
     * @var int
     */
    public const MIN_VALUE = 0;

    /**
     * The maximum value a segment of an IPv4 address can have.
     * @var int
     */
    public const MAX_VALUE = 255;

    /**
     * The number of segments an IPv4 address has.
     * @var int
     */
    public const NUM_SEGMENTS = 4;

    /**
     * The notationt type of this address.
     * @var int
     */
    protected int $notation = self::NOTATION_INVALID;

    /**
     * The subnet set for this ip address.
     * @var Subnet
     */
    protected ?Subnet $subnet = null;

    /**
     * Creates a new object with the given ip and notation value.
     * By default the dot-decimal notation is expected.
     *
     * @param string $ip The ip address
     * @param int $notation The notation type
     */
    public function __construct(string $ip, int $notation = self::NOTATION_DOT_DECIMAL)
    {
        $this->value    = $ip;
        $this->notation = $notation;
        $this->subnet   = null;
        $this->parse();
    }

    /**
     * Returns the value set for this ip address.
     *
     * @return string The ip address
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Parses the given value into the desired format.
     *
     * @return void
     */
    protected function parse(): void
    {
        switch ($this->notation) {

            case self::NOTATION_DOT_DECIMAL:
                $this->fromDotDecimal($this->value);
                break;

            case self::NOTATION_BINARY:
                $this->fromBinary($this->value);
                break;

            case self::NOTATION_CIDR_SHORT:
                $this->fromCidrShort($this->value);
                break;

            case self::NOTATION_CIDR_LONG:
                $this->fromCidrLong($this->value);
                break;

            case self::NOTATION_CIDR_BINARY:
                $this->fromCidrBinary($this->value);
                break;

            default:
                $this->notation = self::NOTATION_INVALID;
        }
    }

    /**
     * Test each segment of the addess to be within the MIN_VALUE and MAX_VALUE.
     *
     * @return bool Returns true if the ip address is valid
     */
    public function isValid(): bool
    {
        $segments = explode('.', $this->value, self::NUM_SEGMENTS);

        if (count($segments) !== self::NUM_SEGMENTS) {
            return false;
        }

        for ($i = 0; $i < self::NUM_SEGMENTS; $i++) {
            $value = (int) $segments[$i];

            if ($value < self::MIN_VALUE || $value > self::MAX_VALUE) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $value
     * @return void
     */
    public function fromBinary(string $value): void
    {
        $this->notation = self::NOTATION_DOT_DECIMAL;

        $data = explode('.', $value, self::NUM_SEGMENTS);

        for ($i = 0; $i < self::NUM_SEGMENTS; $i++) {
            $data[$i] = bindec($data[$i]);
        }

        $this->value = implode('.', $data);

        $this->subnet = null;
    }

    /**
     *
     * @return string
     */
    public function toBinary(): string
    {
        $segments = explode('.', $this->value);

        $data = [];

        for ($i = 0; $i < self::NUM_SEGMENTS; $i++) {
            $data[] = str_pad(decbin($segments[$i]), 8, STR_PAD_LEFT);
        }

        $out = implode('.', $data);

        if ($this->subnet !== null) {
            $out .= $this->subnet->toBin();
        }

        return $out;
    }

    /**
     * @param string $value
     * @return void
     */
    public function fromCidrLong(string $value): void
    {
        $this->notation = self::NOTATION_DOT_DECIMAL;

        [$this->value, $subnet] = explode('/', $value);

        $this->subnet = new Subnet($subnet, Subnet::NOTATION_DOT);
    }

    /**
     *
     * @return string
     */
    public function toCidrLong(): string
    {
        return $this->value . ($this->subnet !== null ? $this->subnet->toDot() : '');
    }

    /**
     * @param string $value
     * @return void
     */
    public function fromCidrShort(string $value): void
    {
        $this->notation = self::NOTATION_DOT_DECIMAL;

        [$this->value, $subnet] = explode('/', $value);

        $this->subnet = new Subnet($subnet, Subnet::NOTATION_CIDR);
    }

    /**
     *
     * @return string
     */
    public function toCidrShort(): string
    {
        return $this->value . ($this->subnet !== null ? $this->subnet->toCIDR() : '');
    }

    /**
     * @param string $value
     * @return void
     */
    public function fromCidrBinary(string $value): void
    {
        $this->notation = self::NOTATION_DOT_DECIMAL;

        [$ip, $subnet] = explode('/', $value);

        $this->subnet = new Subnet($subnet, Subnet::NOTATION_BINARY);

        $data = explode('.', $ip);

        for ($i = 0; $i < self::NUM_SEGMENTS; $i++) {
            $data[$i] = bindec($data[$i]);
        }

        $this->value = implode('.', $data);
    }

    /**
     *
     * @return string
     */
    public function toCidrBinary(): string
    {
        return $this->toBinary() . ($this->subnet !== null ? $this->subnet->toBin() : '');
    }

    /**
     * @return void
     */
    protected function fromDotDecimal(string $value): void
    {
        $this->notation = self::NOTATION_DOT_DECIMAL;
        $this->value = $value;
    }
}

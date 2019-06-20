<?php

namespace hschulz\Network;

use function \array_key_exists;
use function \array_search;
use function \bindec;
use function \decbin;
use function \explode;
use function \implode;
use function \str_pad;
use const \STR_PAD_LEFT;

/**
 *
 */
class Subnet
{

    // <editor-fold defaultstate="collapsed" desc="Class constants">

    /**
     * A value that is used to parse an invalid subnet mask.
     * @var int
     */
    const NOTATION_INVALID = -1;

    /**
     * Output format in dot notation.
     * @example 255.255.255.0
     * @var int
     */
    const NOTATION_DOT = 0;

    /**
     * Output format in CIDR notation.
     * @example /8
     * @var int
     */
    const NOTATION_CIDR = 1;

    /**
     * Output format in binary dot notation
     * @example 11111111.11111111.11111111.11000000
     * @var int
     */
    const NOTATION_BINARY = 2;

    /**
     * The number of segments a subnet mask has.
     * @var int
     */
    const NUM_SEGMENTS = 4;

    /**
     * List of all subnet masks.
     * @var array
     */
    const SUBNET_LIST = [
        0  => '0.0.0.0',
        1  => '128.0.0.0',
        2  => '192.0.0.0',
        3  => '224.0.0.0',       // Class D
        4  => '240.0.0.0',
        5  => '248.0.0.0',
        6  => '252.0.0.0',
        7  => '254.0.0.0',
        8  => '255.0.0.0',       // Class A
        9  => '255.128.0.0',
        10 => '255.192.0.0',
        11 => '255.224.0.0',
        12 => '255.240.0.0',
        13 => '255.248.0.0',
        14 => '255.252.0.0',
        15 => '255.254.0.0',
        16 => '255.255.0.0',     // Class B
        17 => '255.255.128.0',
        18 => '255.255.192.0',
        19 => '255.255.224.0',
        20 => '255.255.240.0',
        21 => '255.255.248.0',
        22 => '255.255.252.0',
        23 => '255.255.254.0',
        24 => '255.255.255.0',   // Class C
        25 => '255.255.255.128',
        26 => '255.255.255.192',
        27 => '255.255.255.224',
        28 => '255.255.255.240',
        29 => '255.255.255.248',
        30 => '255.255.255.252',
        31 => '255.255.255.254',
        32 => '255.255.255.255'  // Class E
    ];

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Class members">

    /**
     *
     * @var string
     */
    protected $value = '';

    /**
     *
     * @var int
     */
    protected $notation = self::NOTATION_INVALID;

    // </editor-fold>

    /**
     *
     * @param string $value
     * @param int $notation
     */
    public function __construct(string $value, int $notation = self::NOTATION_DOT)
    {
        $this->value = $value;
        $this->notation = $notation;
        $this->parse();
    }

    /**
     *
     * @return string
     */
    public function __toString()
    {
        return $this->value;
    }

    /**
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return array_search($this->value, self::SUBNET_LIST) !== false
                ? true : false;
    }

    /**
     *
     * @return bool
     */
    public function isClassA(): bool
    {
        return $this->value === self::SUBNET_LIST[8];
    }

    /**
     *
     * @return bool
     */
    public function isClassB(): bool
    {
        return $this->value === self::SUBNET_LIST[16];
    }

    /**
     *
     * @return bool
     */
    public function isClassC(): bool
    {
        return $this->value === self::SUBNET_LIST[24];
    }

    /**
     *
     * @return bool
     */
    public function isClassD(): bool
    {
        return $this->value === self::SUBNET_LIST[3];
    }

    /**
     *
     * @return bool
     */
    public function isClassE(): bool
    {
        return $this->value === self::SUBNET_LIST[32];
    }

    /**
     *
     */
    protected function parse()
    {
        switch ($this->notation) {

            case self::NOTATION_BINARY:
                $this->fromBin($this->value);
                break;
            case self::NOTATION_CIDR:
                $this->fromCIDR($this->value);
                break;
            case self::NOTATION_DOT:
                $this->fromDot($this->value);
                break;
            default:
                $this->notation = self::NOTATION_INVALID;
        }
    }

    /**
     *
     * @param string $value
     * @return void
     */
    public function fromDot(string $value): void
    {
        $this->value = $value;
        $this->notation = self::NOTATION_DOT;
    }

    /**
     *
     * @param int $value
     * @return void
     */
    public function fromCIDR(int $value): void
    {
        $this->notation = self::NOTATION_INVALID;

        if (array_key_exists($value, self::SUBNET_LIST)) {
            $this->value = self::SUBNET_LIST[$value];

            $this->notation = self::NOTATION_DOT;
        }
    }

    /**
     *
     * @param string $value
     * @return void
     */
    public function fromBin(string $value): void
    {
        $segments = explode('.', $value, self::NUM_SEGMENTS);

        $data = [];

        for ($i = 0; $i < self::NUM_SEGMENTS; $i++) {
            $data[] = bindec($segments[$i]);
        }

        $this->value = implode('.', $data);

        $this->notation = self::NOTATION_DOT;
    }

    /**
     *
     * @return string
     */
    public function toDot(): string
    {
        return $this->value;
    }

    /**
     *
     * @return string
     */
    public function toBin(): string
    {
        $segments = explode('.', $this->value, self::NUM_SEGMENTS);

        for ($i = 0; $i < self::NUM_SEGMENTS; $i++) {
            $segments[$i] = str_pad(decbin($segments[$i]), 8, '0', STR_PAD_LEFT);
        }

        return implode('.', $segments);
    }

    /**
     *
     * @return int
     */
    public function toCIDR()
    {
        $pos = array_search($this->value, self::SUBNET_LIST);
        return $pos !== false ? $pos : -1;
    }
}

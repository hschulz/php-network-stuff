<?php

namespace hschulz\Network;

use const \STR_PAD_LEFT;
use function \array_merge;
use function \array_pad;
use function \count;
use function \explode;
use function \hexdec;
use function \str_pad;
use function \stripos;

/**
 *
 */
class IPv6 extends AbstractIPAddress
{
    /**
     *
     * @var int
     */
    const MIN_VALUE = 0;

    /**
     *
     * @var int
     */
    const MAX_VALUE = 65535;

    /**
     *
     * @var int
     */
    const NUM_SEGMENTS = 8;

    /**
     *
     * @param string $value
     */
    public function __construct($value)
    {
        $this->value = $value;
        $this->normalize();
    }

    protected function normalize()
    {
        // ::1
        // ff06::3
        // ff06:0:0:0:0:0:0:3
        // ff06:0000:0000:0000:0000:0000:0000:0003

        $parts = explode(':', $this->value);

        if (stripos($this->value, '::') !== false) {
            $parts = $this->deflate();
        }

        $processed = [];

        for ($i = 0, $c = count($parts); $i < $c; $i++) {
            $processed[] = str_pad($parts[$i], 4, '0', STR_PAD_LEFT);
        }
    }

    protected function deflate()
    {
        $parts = explode('::', $this->value);

        if (count($parts) !== 2) {
            return [];
        }

        $before = [];

        if (!empty($parts[0])) {
            $before = explode(':', $parts[0]);
        }

        $after  = explode(':', $parts[1]);

        $diff = self::NUM_SEGMENTS - count($before) - count($after);

        if ($diff <= 0) {
            return [];
        }

        $padded = array_pad($before, $diff, '0000');

        return array_merge($padded, $after);
    }

    /**
     *
     * @return bool
     */
    public function isValid()
    {
        $bIsValid = false;

        $aSegments = explode(':', $this->value);

        if (count($aSegments) === self::NUM_SEGMENTS) {
            for ($i = 0; $i < self::NUM_SEGMENTS; $i++) {
                $iSegment = hexdec($aSegments[$i]);

                if ($iSegment >= self::MIN_VALUE && $iSegment <= self::MAX_VALUE) {
                    $bIsValid = true;
                }
            }
        }

        return $bIsValid;
    }
}

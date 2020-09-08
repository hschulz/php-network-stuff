<?php

declare(strict_types=1);

namespace Hschulz\Network;

/**
 *
 */
interface Validatable
{
    public function isValid(): bool;
}

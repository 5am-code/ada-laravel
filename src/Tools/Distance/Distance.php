<?php

namespace Ada\Tools\Distance;

/**
 * Abstract class for neighbor vector distance calculation values.
 */
abstract class Distance
{
    abstract public function getValue(): int;
}

<?php declare(strict_types=1);

namespace App\Domain\ValueObject\Annotation;

/**
 * @Annotation
 * @codeCoverageIgnore
 */
class ObjectsList implements \JsonSerializable
{
    public $object;

    public function jsonSerialize()
    {
        return [$this->object];
    }
}

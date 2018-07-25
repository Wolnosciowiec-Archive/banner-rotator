<?php declare(strict_types = 1);

namespace App\Domain\Exception;

/**
 * @codeCoverageIgnore
 */
class ManagerException extends ApplicationException
{
    const ENTITY_ALREADY_EXISTS = 80001;
}

<?php declare(strict_types = 1);

namespace App\Exception;

class ManagerException extends ApplicationException
{
    const ENTITY_ALREADY_EXISTS = 80001;
}

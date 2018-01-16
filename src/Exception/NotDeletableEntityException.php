<?php declare(strict_types = 1);

namespace App\Exception;

class NotDeletableEntityException extends DataPersistenceException
{
    const AT_LEAST_ONE_ITEM_EXISTING = 60001;
}

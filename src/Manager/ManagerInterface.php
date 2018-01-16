<?php declare(strict_types = 1);

namespace App\Manager;

interface ManagerInterface
{
    public function getEntityClassName(): string;
    public function getRepository();
    public function findOrCreateNew(string $id, bool $createNew);
    public function save($object);
    public function deleteById(string $id);
}

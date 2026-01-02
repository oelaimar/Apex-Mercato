<?php

interface RepositoryInterface
{
    public function save(object $entity): bool;

    public function delete(int $id): bool;

    public function findById(int $id): ?object;

    public function findAll(): array;

    public function update(object $entity): bool;
}

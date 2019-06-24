<?php

namespace core\interfaces;

/**
 * Interface RepositoryInterface
 * @package core\interfaces
 */
interface RepositoryInterface
{
    /**
     * Find Entity by PK or condition
     * @param null $condition
     * @return mixed
     */
    public function find($condition = null);

    public function findAll($condition = null);

    public function save(EntityInterface $Entity);

    public function delete(EntityInterface $Entity): bool;
}

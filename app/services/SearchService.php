<?php

namespace app\services;

use core\interfaces\RepositoryInterface;

/**
 * Class SearchService
 * @package app\services
 */
class SearchService
{
    /** @var RepositoryInterface $_repository */
    protected $_repository;

    /**
     * SearchService constructor.
     * @param RepositoryInterface $repositoryClass
     */
    public function __construct(RepositoryInterface $repositoryClass)
    {
        $this->_repository = $repositoryClass;
    }

    /**
     * @param string $name
     * @return array
     */
    public function search(string $name): array
    {
        return $this->_repository->findAll(' WHERE PATIENT_NAME LIKE "%' . $name . '%"');
    }
}

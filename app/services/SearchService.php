<?php
/**
 * Created by PhpStorm.
 * User: bw
 * Date: 17.03.2019
 * Time: 0:07
 */

namespace app\services;

use core\interfaces\RepositoryInterface;

class SearchService
{
	/**
	 * @var RepositoryInterface $_repository
	 */
	protected $_repository;
	
	public function __construct( RepositoryInterface $repositoryClass )
	{
		$this->_repository = $repositoryClass;
	}
	
	public function search( string $name ): array
	{
		return $this->_repository->findAll( ' WHERE PATIENT_NAME LIKE "%' . $name . '%"' );
	}
	
	
}
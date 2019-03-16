<?php
/**
 * Created by PhpStorm.
 * User: bw
 * Date: 16.03.2019
 * Time: 20:35
 */

namespace core\interfaces;

interface RepositoryInterface
{
	/**
	 * Find Entity by PK or condition
	 * @param null $condition
	 * @return mixed
	 */
	public function find( $condition = null);
	
	public function findAll( $condition = null);
	
	public function save( EntityInterface $Entity );
	
	public function delete( EntityInterface $Entity ): bool;
	
}
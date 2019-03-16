<?php
/**
 * Created by PhpStorm.
 * User: bw
 * Date: 16.03.2019
 * Time: 17:32
 */

namespace app\entities;

use core\Entity;

class Rel extends Entity
{
	protected static $_primaryKey = 'MEDREC_ID';
	
	protected $_medrecId;
	
	protected $_ndc;
	
	public static function tableName()
	{
		return 'tb_rel';
	}
	
	public static function fieldsMapping():array
	{
		return [
			'MEDREC_ID' => 'medrecId',
			'NDC' => 'ndc',
		];
	}
	
	public function getMedrecId(): string
	{
		return (string)$this->_medrecId;
	}
	
	public function setMedrecId( $medrecId )
	{
		return $this->_medrecId = $medrecId;
	}
	
	public function getNdc()
	{
		return $this->_ndc;
	}
	
	public function setNdc($ndc)
	{
		$this->_ndc = $ndc;
	}

}
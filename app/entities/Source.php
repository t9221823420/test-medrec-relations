<?php
/**
 * Created by PhpStorm.
 * User: bw
 * Date: 16.03.2019
 * Time: 17:32
 */

namespace app\entities;

use core\Entity;

class Source extends Entity
{
	protected static $_primaryKey = 'MEDREC_ID';
	
	protected $_medrecId;
	
	protected $_icd;
	
	protected $_patientName;
	
	public static function tableName()
	{
		return 'tb_source';
	}
	
	public static function fieldsMapping(): array
	{
		return [
			'MEDREC_ID'    => 'medrecId',
			'ICD'          => 'icd',
			'PATIENT_NAME' => 'patientName',
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
	
	public function getIcd()
	{
		return $this->_icd;
	}
	
	public function setIcd( $icd )
	{
		$this->_icd = $icd;
	}
	
	public function getPatientName()
	{
		return $this->_patientName;
	}
	
	public function setPatientName( $patientName )
	{
		$this->_patientName = $patientName;
	}
}
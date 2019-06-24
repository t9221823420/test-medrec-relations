<?php

namespace app\entities;

use core\Entity;

/**
 * Class Rel
 * @package app\entities
 */
class Rel extends Entity
{
    /** @var string  */
	protected static $_primaryKey = 'MEDREC_ID';
	/** @var  */
	protected $_medrecId;
	/** @var  */
	protected $_ndc;

    /**
     * @return string
     */
	public static function tableName()
	{
		return 'tb_rel';
	}

    /**
     * @return array
     */
	public static function fieldsMapping():array
	{
		return [
			'MEDREC_ID' => 'medrecId',
			'NDC' => 'ndc',
		];
	}

    /**
     * @return string
     */
	public function getMedrecId(): string
	{
		return (string)$this->_medrecId;
	}

    /**
     * @param $medrecId
     * @return mixed
     */
	public function setMedrecId( $medrecId )
	{
		return $this->_medrecId = $medrecId;
	}

    /**
     * @return mixed
     */
	public function getNdc()
	{
		return $this->_ndc;
	}

    /**
     * @param $ndc
     */
	public function setNdc($ndc)
	{
		$this->_ndc = $ndc;
	}
}

<?php

namespace app\entities;

use core\Entity;

/**
 * Class Source
 * @package app\entities
 */
class Source extends Entity
{
    /** @var string */
    protected static $_primaryKey = 'MEDREC_ID';
    /** @var int */
    protected $_medrecId;
    /** @var string */
    protected $_icd;
    /** @var string */
    protected $_patientName;

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'tb_source';
    }

    /**
     * @return array
     */
    public static function fieldsMapping(): array
    {
        return [
            'MEDREC_ID' => 'medrecId',
            'ICD' => 'icd',
            'PATIENT_NAME' => 'patientName',
        ];
    }

    /**
     * @return string
     */
    public function getMedrecId(): string
    {
        return (string)$this->_medrecId;
    }

    public function setMedrecId($medrecId)
    {
        return $this->_medrecId = $medrecId;
    }

    /**
     * @return mixed
     */
    public function getIcd()
    {
        return $this->_icd;
    }

    /**
     * @param $icd
     * @return string
     */
    public function setIcd($icd): string
    {
        $this->_icd = $icd;
    }

    /**
     * @return string
     */
    public function getPatientName(): string
    {
        return $this->_patientName;
    }

    /**
     * @param $patientName
     */
    public function setPatientName($patientName)
    {
        $this->_patientName = $patientName;
    }
}

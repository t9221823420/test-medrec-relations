<?php

namespace core\interfaces;

/**
 * Interface EntityInterface
 * @package core\interfaces
 */
interface EntityInterface
{
    public static function tableName();

    public static function tableNamePrefix();

    public static function primaryKey();

    public static function fieldsMapping(): array;
}

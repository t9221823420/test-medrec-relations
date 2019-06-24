<?php
/**
 * Created by PhpStorm.
 * User: bw
 * Date: 16.03.2019
 * Time: 20:43
 */

namespace core;

use core\interfaces\EntityInterface;

abstract class Entity implements EntityInterface
{
    /**
     * @return string
     */
	public static function tableNamePrefix()
	{
		return '';
	}

	public function __construct( array $attributes = [] )
	{
		$mapping = static::fieldsMapping();

		foreach( $attributes as $attributeName => $value ) {

			if( isset( $mapping[ $attributeName ] ) ) {

				$this->{"_".$mapping[ $attributeName ]} = $value;

			}
		}

	}

	public static function primaryKey( $mapped = false )
	{
		return $mapped ? static::$_primaryKey : static::fieldsMapping()[ static::$_primaryKey ];
	}
}

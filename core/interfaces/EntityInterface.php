<?php
/**
 * Created by PhpStorm.
 * User: bw
 * Date: 16.03.2019
 * Time: 17:27
 */

namespace core\interfaces;

interface EntityInterface
{
	public static function tableName();
	
	public static function tableNamePrefix();
	
	public static function primaryKey();
	
	public static function fieldsMapping():array;
	
	//public function getId(): string;
}
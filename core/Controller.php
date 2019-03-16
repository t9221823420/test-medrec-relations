<?php
/**
 * Created by PhpStorm.
 * User: bw
 * Date: 16.03.2019
 * Time: 12:52
 */

namespace core;

use core\interfaces\ViewContextInterface;

class Controller extends BaseObject implements ViewContextInterface
{
	protected $_viewPath;
	
	protected $id;
	
	public function __construct()
	{
		$this->_id = strtolower( str_replace( 'Controller', '', ( new \ReflectionClass( $this ) )->getShortName() ) );
	}
	
	public function runAction( $actionID, $params )
	{
		$actionPrefix = 'action';
		
		if( method_exists( $this, $actionPrefix . ucfirst( $actionID ) ) ) {
			return call_user_func_array( [ $this, $actionPrefix . ucfirst( $actionID ) ], $params );
		}
		
		throw new \Exception( "action $actionID does not exists" );
		
	}
	
	public function getId()
	{
		return $this->_id;
	}
	
	public function getViewPath( $mixed = null, $sufix = null )
	{
		if( $mixed ) {
			
			if( $mixed instanceof \ReflectionClass ) {
				$class = $mixed;
			}
			else if( is_string( $mixed ) && $sufix ) {
				$class = new \ReflectionClass( $mixed );
			}
			else {
				$class = new \ReflectionClass( $this );
				$sufix = $mixed;
			}
			
		}
		else {
			$class = new \ReflectionClass( $this );
		}
		
		while( ( $class = $class->getParentClass() ) && $class->implementsInterface( ViewContextInterface::class ) ) {
			
			$path = dirname( $class->getFileName() ) . ( $sufix ? DIRECTORY_SEPARATOR . $sufix : null );
			
			if( is_dir( $path ) ) {
				return $path;
			}
		}
		
	}
}
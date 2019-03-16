<?php
/**
 * Created by PhpStorm.
 * User: bw
 * Date: 16.03.2019
 * Time: 12:52
 */

namespace core;

use core\interfaces\ViewContextInterface;

class View
{
	protected $_name;
	protected $_params;
	protected $_viewPath;
	
	public $defaultExtension = 'php';
	
	public function __construct( $viewName, $params )
	{
		$this->_name   = $viewName;
		$this->_params = $params;
	}
	
	public function render( $viewName = null, $params = [] ): string
	{
		if( $viewName ) {
			$this->_name = $viewName;
		}
		
		if( $params ) {
			$this->_params = $params;
		}
		
		$this->_viewPath = $this->_findViewFile( $this->_name );
		
		return $this->renderFile( $this->_viewPath, $this->_params );
	}
	
	public function __toString()
	{
		return $this->render();
	}
	
	protected function _findViewFile( $viewName )
	{
		if( strncmp( $viewName, '@', 1 ) === 0 ) {
			// e.g. "@app/views/main"
			$file = App::getAlias( $viewName );
		}
		else if( strncmp( $viewName, '//', 2 ) === 0 ) {
			// e.g. "//layouts/main"
			$file = $this->getViewPath() . DIRECTORY_SEPARATOR . ltrim( $viewName, '/' );
		}
		else if( App::$instance->controller instanceof Controller ) {
			
			$file = App::$instance->controller->getViewPath()  . DIRECTORY_SEPARATOR . ltrim( $viewName, '/' ) ;
			
		}
		else {
			throw new \Exception( "Unable to locate view file for view '$viewName': no active controller." );
		}
		
		if( pathinfo( $file, PATHINFO_EXTENSION ) !== '' ) {
			return $file;
		}
		$path = $file . '.' . $this->defaultExtension;
		if( $this->defaultExtension !== 'php' && !is_file( $path ) ) {
			$path = $file . '.php';
		}
		
		return $path;
	}
	
	public function getViewPath()
	{
		if( $this->_viewPath === null ) {
			$this->_viewPath = App::$instance->getBasePath() . DIRECTORY_SEPARATOR . 'views';
			//$this->_viewPath = App::$instance->getBasePath();
		}
		
		return $this->_viewPath;
	}
	
	public function renderFile( $_file_, $_params_ = [] )
	{
		$_obInitialLevel_ = ob_get_level();
		ob_start();
		ob_implicit_flush( false );
		extract( $_params_, EXTR_OVERWRITE );
		try {
			require $_file_;
			
			return ob_get_clean();
		} catch( \Exception $e ) {
			while( ob_get_level() > $_obInitialLevel_ ) {
				if( !@ob_end_clean() ) {
					ob_clean();
				}
			}
			throw $e;
		} catch( \Throwable $e ) {
			while( ob_get_level() > $_obInitialLevel_ ) {
				if( !@ob_end_clean() ) {
					ob_clean();
				}
			}
			throw $e;
		}
	}
}
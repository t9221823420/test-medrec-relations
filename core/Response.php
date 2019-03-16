<?php
/**
 * Created by PhpStorm.
 * User: bw
 * Date: 16.03.2019
 * Time: 12:59
 */

namespace core;

class Response
{
	protected $_view;
	
	public function __construct( View $View = null ) {
		
		$this->_view = $View;
	}
	
	public function send(){
		
		if( $this->_view instanceof View ){
			print $this->_view;
		}
		
	}
}
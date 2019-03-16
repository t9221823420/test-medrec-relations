<?php
/**
 * Created by PhpStorm.
 * User: bw
 * Date: 16.03.2019
 * Time: 12:51
 */

namespace core;

class App extends Base
{
	
	public function run()
	{
		$Request = new Request();
		
		if( $Response = $this->_handleRequest( $Request ) ) {
			$Response->send();
			
			return;
		}
		
		throw new \Exception( 'Shit happens' );
		
	}
	

}
<?php

namespace core;

/**
 * Class Request
 * @package core
 */
class Request
{
    /** @var  */
	protected $_url;
	/** @var  */
	protected $_queryParams;
	/** @var  */
	protected $_baseUrl;
	/** @var  */
	protected $_hostName;
	/** @var  */
	protected $_scriptFile;
	/** @var  */
	protected $_scriptUrl;
	/** @var  */
	protected $_pathInfo;

    /**
     * @return mixed|string|string[]|null
     * @throws \Exception
     */
	public function getUrl()
	{
		if( $this->_url === null ) {
			$this->_url = $this->_resolveRequestUri();
		}
		
		return $this->_url;
	}

    /**
     * @return mixed
     */
	public function getHostName()
	{
		if( $this->_hostName === null ) {
			$this->_hostName = parse_url( $this->getHostInfo(), PHP_URL_HOST );
		}
		
		return $this->_hostName;
	}

    /**
     * @return mixed|string
     * @throws \Exception
     */
	public function getScriptUrl()
	{
		if( $this->_scriptUrl === null ) {
			$scriptFile = $this->getScriptFile();
			$scriptName = basename( $scriptFile );
			if( isset( $_SERVER['SCRIPT_NAME'] ) && basename( $_SERVER['SCRIPT_NAME'] ) === $scriptName ) {
				$this->_scriptUrl = $_SERVER['SCRIPT_NAME'];
			}
			else if( isset( $_SERVER['PHP_SELF'] ) && basename( $_SERVER['PHP_SELF'] ) === $scriptName ) {
				$this->_scriptUrl = $_SERVER['PHP_SELF'];
			}
			else if( isset( $_SERVER['ORIG_SCRIPT_NAME'] ) && basename( $_SERVER['ORIG_SCRIPT_NAME'] ) === $scriptName ) {
				$this->_scriptUrl = $_SERVER['ORIG_SCRIPT_NAME'];
			}
			else if( isset( $_SERVER['PHP_SELF'] ) && ( $pos = strpos( $_SERVER['PHP_SELF'], '/' . $scriptName ) ) !== false ) {
				$this->_scriptUrl = substr( $_SERVER['SCRIPT_NAME'], 0, $pos ) . '/' . $scriptName;
			}
			else if( !empty( $_SERVER['DOCUMENT_ROOT'] ) && strpos( $scriptFile, $_SERVER['DOCUMENT_ROOT'] ) === 0 ) {
				$this->_scriptUrl = str_replace( [ $_SERVER['DOCUMENT_ROOT'], '\\' ], [ '', '/' ], $scriptFile );
			}
			else {
				throw new InvalidConfigException( 'Unable to determine the entry script URL.' );
			}
		}
		
		return $this->_scriptUrl;
	}

    /**
     * @return mixed
     * @throws \Exception
     */
	public function getScriptFile()
	{
		if( isset( $this->_scriptFile ) ) {
			return $this->_scriptFile;
		}
		
		if( isset( $_SERVER['SCRIPT_FILENAME'] ) ) {
			return $_SERVER['SCRIPT_FILENAME'];
		}
		
		throw new \Exception( 'Unable to determine the entry script file path.' );
	}

    /**
     * @param $value
     */
	public function setScriptUrl( $value )
	{
		$this->_scriptUrl = $value === null ? null : '/' . trim( $value, '/' );
	}

    /**
     * @return mixed|string|string[]|null
     * @throws \Exception
     */
	protected function _resolveRequestUri()
	{
		if( isset( $_SERVER['REQUEST_URI'] ) ) {
			$requestUri = $_SERVER['REQUEST_URI'];
			if( $requestUri !== '' && $requestUri[0] !== '/' ) {
				$requestUri = preg_replace( '/^(http|https):\/\/[^\/]+/i', '', $requestUri );
			}
		}
		else if( isset( $_SERVER['ORIG_PATH_INFO'] ) ) { // IIS 5.0 CGI
			$requestUri = $_SERVER['ORIG_PATH_INFO'];
			if( !empty( $_SERVER['QUERY_STRING'] ) ) {
				$requestUri .= '?' . $_SERVER['QUERY_STRING'];
			}
		}
		else {
			throw new \Exception( 'Unable to determine the request URI.' );
		}
		
		return $requestUri;
	}

    /**
     * @return string
     * @throws \Exception
     */
	protected function _resolvePathInfo()
	{
		$pathInfo = $this->getUrl();
		
		if( ( $pos = strpos( $pathInfo, '?' ) ) !== false ) {
			$pathInfo = substr( $pathInfo, 0, $pos );
		}
		
		$pathInfo = urldecode( $pathInfo );
		
		// try to encode in UTF8 if not so
		// http://w3.org/International/questions/qa-forms-utf-8.html
		if( !preg_match( '%^(?:
            [\x09\x0A\x0D\x20-\x7E]              # ASCII
            | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
            | \xE0[\xA0-\xBF][\x80-\xBF]         # excluding overlongs
            | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
            | \xED[\x80-\x9F][\x80-\xBF]         # excluding surrogates
            | \xF0[\x90-\xBF][\x80-\xBF]{2}      # planes 1-3
            | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
            | \xF4[\x80-\x8F][\x80-\xBF]{2}      # plane 16
            )*$%xs', $pathInfo )
		) {
			$pathInfo = $this->utf8Encode( $pathInfo );
		}
		
		$scriptUrl = $this->getScriptUrl();
		$baseUrl   = $this->getBaseUrl();
		
		if( strpos( $pathInfo, $scriptUrl ) === 0 ) {
			$pathInfo = substr( $pathInfo, strlen( $scriptUrl ) );
		}
		else if( $baseUrl === '' || strpos( $pathInfo, $baseUrl ) === 0 ) {
			$pathInfo = substr( $pathInfo, strlen( $baseUrl ) );
		}
		else if( isset( $_SERVER['PHP_SELF'] ) && strpos( $_SERVER['PHP_SELF'], $scriptUrl ) === 0 ) {
			$pathInfo = substr( $_SERVER['PHP_SELF'], strlen( $scriptUrl ) );
		}
		else {
			throw new InvalidConfigException( 'Unable to determine the path info of the current request.' );
		}
		
		if( strncmp( $pathInfo, '/', 1 ) === 0 ) {
			$pathInfo = substr( $pathInfo, 1 );
		}
		
		return (string)$pathInfo;
	}

    /**
     * @param $s
     * @return bool|string
     */
	public static function utf8Encode( $s )
	{
		$s   .= $s;
		$len = \strlen( $s );
		for( $i = $len >> 1, $j = 0; $i < $len; ++$i, ++$j ) {
			switch( true ) {
				case $s[ $i ] < "\x80":
					$s[ $j ] = $s[ $i ];
					break;
				case $s[ $i ] < "\xC0":
					$s[ $j ]   = "\xC2";
					$s[ ++$j ] = $s[ $i ];
					break;
				default:
					$s[ $j ]   = "\xC3";
					$s[ ++$j ] = \chr( \ord( $s[ $i ] ) - 64 );
					break;
			}
		}
		
		return substr( $s, 0, $j );
	}

    /**
     * @return array
     */
	public function resolve()
	{
		$result = $this->parseRequest();
		
		if( $result !== false ) {
			list( $route, $params ) = $result;
			if( $this->_queryParams === null ) {
				$_GET = $params + $_GET; // preserve numeric keys
			}
			else {
				$this->_queryParams = $params + $this->_queryParams;
			}
			
			return [ $route, $this->getQueryParams() ];
		}
		
		throw new NotFoundHttpException( Yii::t( 'yii', 'Page not found.' ) );
	}

    /**
     * @return string
     * @throws \Exception
     */
	public function getPathInfo()
	{
		if( $this->_pathInfo === null ) {
			$this->_pathInfo = $this->_resolvePathInfo();
		}
		
		return $this->_pathInfo;
	}

    /**
     * @return array
     * @throws \Exception
     */
	public function parseRequest()
	{
		$pathInfo = $this->getPathInfo();
		
		return [ $pathInfo, [] ];
	}

    /**
     * @return mixed
     */
	public function getQueryParams()
	{
		if( $this->_queryParams === null ) {
			return $_GET;
		}
		
		return $this->_queryParams;
	}

    /**
     * @return string
     * @throws \Exception
     */
	public function getBaseUrl()
	{
		if( $this->_baseUrl === null ) {
			$this->_baseUrl = rtrim( dirname( $this->getScriptUrl() ), '\\/' );
		}
		
		return $this->_baseUrl;
	}
}

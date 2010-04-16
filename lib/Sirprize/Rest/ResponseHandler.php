<?php

/**
 * REST client for PHP 5.3+ 
 *
 * LICENSE
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt
 *
 * @category   Sirprize
 * @package    Flickr
 * @copyright  Copyright (c) 2010, Christian Hoegl, Switzerland (http://sirprize.me)
 * @license    MIT License
 */


namespace Sirprize\Rest;


class ResponseHandler
{
    
	
    protected $_httpResponse = null;
	protected $_loaded = false;
	protected $_serviceErrorCode = null;
	protected $_serviceErrorMessage = null;
	
	
	
	
	public function setHttpResponse(\Zend_Http_Response $httpResponse)
    {
		if($this->_httpResponse !== null)
		{
			require_once 'Sirprize/Rest/Exception.php';
			throw new \Sirprize\Rest\Exception('setHttpResponse() has already been called');
		}
		
    	$this->_httpResponse = $httpResponse;
		return $this;
    }
	
    
    
    public function getHttpResponse()
    {
		if($this->_httpResponse === null)
		{
			require_once 'Sirprize/Rest/Exception.php';
			throw new \Sirprize\Rest\Exception('call setHttpResponse() before '.__METHOD__);
		}
		
    	return $this->_httpResponse;
    }
	
	
	
	
	public function getErrorCode()
    {
		if($this->_serviceErrorCode !== null)
		{
			return $this->_serviceErrorCode;
		}
		
		if($this->getHttpResponse()->isError())
		{
			return $this->getHttpResponse()->getStatus();
		}
		
		return null;
    }
    
    
    
    public function getErrorMessage()
    {
		if($this->_serviceErrorMessage !== null)
		{
			return $this->_serviceErrorMessage;
		}
		
		if($this->getHttpResponse()->isError())
		{
			return $this->getHttpResponse()->getMessage();
		}
		
		return null;
    }
	
    
    
    public function isError()
    {
    	return (
    		$this->getHttpResponse()->isError() ||
    		$this->getErrorCode() !== null ||
    		$this->getErrorMessage() !== null
    	);
    }
	
	
	
	public function handleErrors($errno, $errstr, $errfile = null, $errline = null, array $errcontext = null)
	{
		require_once 'Sirprize/Rest/Exception.php';
		throw new \Sirprize\Rest\Exception($errstr);
	}
	
	
	
	public function isLoaded()
    {
    	return $this->_loaded;
    }
	
	
	
	public function load()
    {
		$this->_makeLoadCheck();
    	return $this;
    }
	
	
	
	protected function _makeLoadCheck()
    {
		if($this->getHttpResponse()->isError())
		{
			require_once 'Sirprize/Rest/Exception.php';
			throw new \Sirprize\Rest\Exception('response can only be loaded from a successful http response');
		}
		
		if($this->_loaded)
		{
			require_once 'Sirprize/Rest/Exception.php';
			throw new \Sirprize\Rest\Exception('response already loaded');
		}
    }
	
}
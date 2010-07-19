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
	protected $_code = null;
	protected $_message = null;
	
	
	
	
	public function setHttpResponse(\Zend_Http_Response $httpResponse)
    {
		if($this->_httpResponse !== null)
		{
			throw new \Sirprize\Rest\Exception('setHttpResponse() has already been called');
		}
		
    	$this->_httpResponse = $httpResponse;
		return $this;
    }
	
    
    
    public function getHttpResponse()
    {
		if($this->_httpResponse === null)
		{
			throw new \Sirprize\Rest\Exception('call setHttpResponse() before '.__METHOD__);
		}
		
    	return $this->_httpResponse;
    }
	
	
	
	
	public function getCode()
    {
		if($this->_code !== null)
		{
			return $this->_code;
		}
		
		if($this->getHttpResponse()->isError())
		{
			return $this->getHttpResponse()->getStatus();
		}
		
		return null;
    }
    
    
    
    public function getMessage()
    {
		if($this->_message !== null)
		{
			return $this->_message;
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
    		$this->getCode() !== null ||
    		$this->getMessage() !== null
    	);
    }
	
	
	
	public function handleErrors($errno, $errstr, $errfile = null, $errline = null, array $errcontext = null)
	{
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
		/*
		if($this->getHttpResponse()->isError())
		{
			#require_once 'Sirprize/Rest/Exception.php';
			throw new \Sirprize\Rest\Exception('response can only be loaded from a successful http response');
		}
		*/
		if($this->_loaded)
		{
			throw new \Sirprize\Rest\Exception('response already loaded');
		}
    }
	
}
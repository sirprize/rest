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
 * @package    Sirprize\Rest
 * @copyright  Copyright (c) 2010, Christian Hoegl, Switzerland (http://sirprize.me)
 * @license    MIT License
 */


namespace Sirprize\Rest;


class ResponseHandler
{
    
	
    protected $_httpResponse = null;
	protected $_code = null;
	protected $_message = null;
	
	
	
	protected function _reset()
	{
		$this->_httpResponse = null;
		$this->_code = null;
		$this->_message = null;
		return $this;
	}
    
    
	
    public function getHttpResponse()
    {
		if($this->_httpResponse === null)
		{
			throw new \Sirprize\Rest\Exception('call load() before '.__METHOD__);
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
	
	
	
	public function handleLoadErrors($errno, $errstr, $errfile = null, $errline = null, array $errcontext = null)
	{
		throw new \Sirprize\Rest\Exception($errstr);
	}
	
	
	
	public function isLoaded()
    {
    	return ($this->_httpResponse !== null);
    }
	
	
	
	public function load(\Zend_Http_Response $httpResponse)
    {
		$this->_reset();
    	$this->_httpResponse = $httpResponse;
    	return $this;
    }
	
}
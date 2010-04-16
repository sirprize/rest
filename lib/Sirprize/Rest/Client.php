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


class Client
{
	
	
    protected $_httpClient = null;
	protected $_cache = null;
    protected $_uri = null;
	protected $_responseHandler = null;
	protected $_cacheId = null;
	
	
    public function __construct(array $config = array())
    {}
	
	
    public function setHttpClient(\Zend_Http_Client $httpClient)
    {
        $this->_httpClient = $httpClient;
		return $this;
    }
	
	
    protected function _getHttpClient()
    {
        if($this->_httpClient === null)
		{
			require_once 'Zend/Http/Client.php';
            $this->_httpClient = new \Zend_Http_Client();
        }

        return $this->_httpClient;
    }
	
	
	public function setCache(\Zend_Cache_Core $cache = null)
	{
		$this->_cache = $cache;
		return $this;
	}
	
	
	protected function _getCache()
    {
        return $this->_cache;
    }
	
	
	public function setCacheId($cacheId)
	{
		$this->_cacheId = $cacheId;
		return $this;
	}
	
	
	public function setCacheIdFromParts($parts)
	{
		$this->_cacheId = preg_replace('/[^a-zA-Z0-9_]/', '_', implode('_', $parts));
		return $this;
	}
	
	
    public function setUri(\Zend_Uri $uri)
    {
        $this->_uri = $uri;
        return $this;
    }
	
	
    public function getUri($throwException = true)
    {
		if($this->_uri === null && $throwException)
		{
			require_once 'Sirprize/Rest/Exception.php';
			throw new \Sirprize\Rest\Exception('call setUri before '.__METHOD__);
		}
		
        return $this->_uri;
    }
	
	
	public function setResponseHandler(\Sirprize\Rest\ResponseHandler $responseHandler)
	{
		$this->_responseHandler = $responseHandler;
		return $this;
	}
	
	
	protected function _getResponseHandler()
	{
		if($this->_responseHandler === null)
		{
			require_once 'Sirprize/Rest/ResponseHandler.php';
		 	$this->_responseHandler = new \Sirprize\Rest\ResponseHandler();
		}
		
		return $this->_responseHandler;
	}
	
	
	public function get(array $args = array(), array $headers = array())
    {
		$httpResponse = false;
		$responseHandler = $this->_getResponseHandler(); // keep local reference
		
		if($this->_getCache() && $this->_cacheId)
		{
			require_once 'Zend/Http/Response.php';
			$httpResponse = $this->_getCache()->load($this->_cacheId);
		}
		
		if(!$httpResponse)
		{
			$httpResponse = $this->_getHttpClient()
				->resetParameters()
				->setUri($this->getUri())
				->setHeaders($headers)
				->setParameterGet($args)
				->request('GET')
			;
		}
		
		$responseHandler->setHttpResponse($httpResponse);
		
		if($httpResponse->isError())
		{
			$this->_resetRequestHandlingParams();
			return $responseHandler;
		}
		
		$responseHandler->load($httpResponse);
		
		if($this->_getCache() && $this->_cacheId)
		{
			$this->_getCache()->save($httpResponse, $this->_cacheId);
		}
		
		$this->_resetRequestHandlingParams();
		return $responseHandler;
    }
	

	
	
	public function post(array $args = array(), array $headers = array())
    {
		$responseHandler = $this->_getResponseHandler(); // keep local reference
		
		$httpResponse = $this->_getHttpClient()
			->resetParameters()
			->setUri($this->getUri())
			->setHeaders($headers)
			->setParameterPost($args)
			->request('POST')
		;
		
		$responseHandler
			->setHttpResponse($httpResponse)
			->load($httpResponse)
		;
		
        $this->_resetRequestHandlingParams();
		return $responseHandler;
    }

	
	
	public function postRaw($data = null, array $headers = array())
    {
		$responseHandler = $this->_getResponseHandler(); // keep local reference
		
		$httpResponse = $this->_getHttpClient()
			->resetParameters()
			->setUri($this->getUri())
			->setHeaders($headers)
			->setRawData($data)
			->request('POST')
		;
		
        $responseHandler
			->setHttpResponse($httpResponse)
			->load($httpResponse)
		;
		
        $this->_resetRequestHandlingParams();
		return $responseHandler;
    }

	
	
	public function put(array $args = array(), array $headers = array())
    {
		$responseHandler = $this->_getResponseHandler(); // keep local reference
		
		$httpResponse = $this->_getHttpClient()
			->resetParameters()
			->setUri($this->getUri())
			->setHeaders($headers)
			->setParameterPost($args)
			->request('PUT')
		;
		
        $responseHandler
			->setHttpResponse($httpResponse)
			->load($httpResponse)
		;
		
        $this->_resetRequestHandlingParams();
		return $responseHandler;
    }

	
	
	public function putRaw($data = null, array $headers = array())
    {
		$responseHandler = $this->_getResponseHandler(); // keep local reference
		
		$httpResponse = $this->_getHttpClient()
			->resetParameters()
			->setUri($this->getUri())
			->setHeaders($headers)
			->setRawData($data)
			->request('PUT')
		;
		
        $responseHandler
			->setHttpResponse($httpResponse)
			->load($httpResponse)
		;
		
        $this->_resetRequestHandlingParams();
		return $responseHandler;
    }
	
	
	
	public function delete(array $headers = array())
    {
		$responseHandler = $this->_getResponseHandler(); // keep local reference
		
		$httpResponse = $this->_getHttpClient()
			->resetParameters()
			->setUri($this->getUri())
			->setHeaders($headers)
			->request('DELETE')
		;
        
		$responseHandler
			->setHttpResponse($httpResponse)
			->load($httpResponse)
		;
		
        $this->_resetRequestHandlingParams();
		return $responseHandler;
    }
	
	
	
	protected function _resetRequestHandlingParams()
	{
		$this->_responseHandler = null;
		$this->_cacheId = null;
		return $this;
	}

}

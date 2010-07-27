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


class Client
{
	
	
    protected $_httpClient = null;
	protected $_cache = null;
	
	
    public function setHttpClient(\Zend_Http_Client $httpClient)
    {
        $this->_httpClient = $httpClient;
		return $this;
    }
	
	
    public function getHttpClient()
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
	
	
	public function getCache()
    {
        return $this->_cache;
    }
	
	
	public static function makeCacheIdFromParts($parts)
	{
		return preg_replace('/[^a-zA-Z0-9_]/', '_', implode('_', $parts));
	}
	
	
	public function get(\Sirprize\Rest\ResponseHandler $responseHandler, $numRetries = 0, $acceptableHttpErrorCodes = array(), $cacheId = null)
    {
		$httpResponse = false;
		$fromCache = true;
		
		if($this->getCache() && $cacheId)
		{
			#require_once 'Zend/Http/Response.php';
			$httpResponse = $this->getCache()->load($cacheId);
		}
		
		if(!$httpResponse)
		{
			// response could not be loaded from cache, time to connect to service
			$fromCache = false;
			$httpResponse = $this->_request('GET', $numRetries, $acceptableHttpErrorCodes);
		}
		
		$responseHandler->load($httpResponse);
		
		if($responseHandler->isError())
		{
			return $responseHandler;
		}
		
		if($this->getCache() && $cacheId && !$fromCache)
		{
			$this->getCache()->save($httpResponse, $cacheId);
		}
		
		return $responseHandler;
    }

	
	
	public function head(\Sirprize\Rest\ResponseHandler $responseHandler)
    {
		$httpResponse = $this->getHttpClient()->request('HEAD');
		return $responseHandler->load($httpResponse);
    }
	
	
	
	public function post(\Sirprize\Rest\ResponseHandler $responseHandler)
    {
		$httpResponse = $this->getHttpClient()->request('POST');
		return $responseHandler->load($httpResponse);
    }

	
	
	public function put(\Sirprize\Rest\ResponseHandler $responseHandler, $numRetries = 0, $acceptableHttpErrorCodes = array())
    {
		$httpResponse = $this->_request('PUT', $numRetries, $acceptableHttpErrorCodes);
        return $responseHandler->load($httpResponse);
    }
	
	
	
	public function delete(\Sirprize\Rest\ResponseHandler $responseHandler)
    {
		$httpResponse = $this->getHttpClient()->request('DELETE');
		return $responseHandler->load($httpResponse);
    }
	
	
	
	protected function _request($verb, $numRetries, $acceptableHttpErrorCodes = array())
	{
		$httpResponse = false;
		$attempts = 0;
		$isOk = false;
		$exception = null;
		
		while($attempts < $numRetries + 1 && !$isOk)
		{
			$attempts++;
			
			try {
				$httpResponse = $this->getHttpClient()->request($verb);
				
				$isOk
					= (in_array($httpResponse->getStatus(), $acceptableHttpErrorCodes)) // eg don't retry if received status 412 from an amazon s3 copy request with x-amz-copy-source-if-match header
					? true
					: !$httpResponse->isError()
				;
			}
			catch(\Exception $e) {
				#print $attempts."\n";
				$exception = $e;
			}
		}
		
		if(!$httpResponse)
		{
			throw $exception;
		}
		
		return $httpResponse;
	}

}
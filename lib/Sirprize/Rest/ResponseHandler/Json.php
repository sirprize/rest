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


namespace Sirprize\Rest\ResponseHandler;


class Json extends \Sirprize\Rest\ResponseHandler
{
    
	
	protected $_json = null;
	
	
	
	public function load(\Zend_Http_Response $httpResponse)
    {
		$this->_reset();
		
		if($httpResponse->getBody())
		{
			set_error_handler(array($this, 'handleLoadErrors'));
			$this->_json = json_decode($httpResponse->getBody());
			restore_error_handler();
		}
		
		$this->_httpResponse = $httpResponse;
    	return $this;
    }
	
	
	
	public function getJson()
    {
    	return $this->_json;
    }
	
}
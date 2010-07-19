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


namespace Sirprize\Rest\ResponseHandler;


class Dom extends \Sirprize\Rest\ResponseHandler
{
    
	
	protected $_dom = null;
	
	
	
	public function load()
    {
		$this->_makeLoadCheck();
		
		if($this->getHttpResponse()->getBody())
		{
			set_error_handler(array($this, 'handleErrors'));
			$this->_dom = new \DOMDocument();
			$this->_dom->loadXml($this->getHttpResponse()->getBody());
			restore_error_handler();
		}
		
		$this->_loaded = true;
    	return $this;
    }
	
	
	
	public function getDom()
    {
    	return $this->_dom;
    }
	
}
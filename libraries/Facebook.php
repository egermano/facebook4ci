<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Copyright 2011 Facebook, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *		 http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

require_once "base_facebook.php";

/**
 * Extends the BaseFacebook class with the intent of using
 * PHP sessions to store user ids and access tokens.
 */
class Facebook extends BaseFacebook
{
	/**
	* Identical to the parent constructor, except that
	* we start a PHP session to store the user ID and
	* access token if during the course of execution
	* we discover them.
	*
	* @param Array $config the application configuration.
	* @see BaseFacebook::__construct in facebook.php
	*/
	
	private  $_ci;
	
	public function __construct($config) 
	{
		if ( ! session_id())
		{
			session_start();
		}
		parent::__construct($config);
		$this->_ci =& get_instance();
	}
		
	/**
     * get Session
     *
     * @access	public
     * @return	array
     */	
	public function get_session()
	{
		return $this->_ci->session->userdata('fb_data');
	}
	
	/**
     * FB auth
     *
     * @access	public
     * @return	boolean
     */	
	public function auth()
	{
		$user = $this->getUser();
		$profile = NULL;

		if ($user)
		{
			try 
			{
				$profile = $this->api('/'.$user);
			}
			catch (FacebookApiException $e) 
			{
				error_log($e);
				$user = NULL;
			}
		}
		
		$fb_data = array(
						'me' => $profile,
						'uid' => $user,
						'loginUrl' => $this->getLoginUrl(),
						'logoutUrl' => $this->getLogoutUrl(),
						);

		$this->_ci->session->set_userdata('fb_data', $fb_data);
	}
	

	public function getUser()
	{
		$user = parent::getUser();
		
		if (empty($user))
		{
			$user = $this->_ci->session->userdata('fb_user_id');
		}
		
		return $user;
	}
	
	public function setUser($user_id)
	{
		$this->_ci->session->set_userdata('fb_user_id', $user_id);
	}

	protected static $kSupportedKeys = array('state', 'code', 'access_token', 'user_id');
	

	/**
	 * Provides the implementations of the inherited abstract
	 * methods.	The implementation uses PHP sessions to maintain
	 * a store for authorization codes, user ids, CSRF states, and
	 * access tokens.
	 */
	protected function setPersistentData($key, $value) 
	{
		if ( ! in_array($key, self::$kSupportedKeys)) 
		{
			self::errorLog('Unsupported key passed to setPersistentData.');
			return;
		}

		$session_var_name = $this->constructSessionVariableName($key);
		$_SESSION[$session_var_name] = $value;
	}

	protected function getPersistentData($key, $default = false) 
	{
		if ( ! in_array($key, self::$kSupportedKeys)) 
		{
			self::errorLog('Unsupported key passed to getPersistentData.');
			return $default;
		}

		$session_var_name = $this->constructSessionVariableName($key);
		return isset($_SESSION[$session_var_name]) ? $_SESSION[$session_var_name] : $default;
	}

	protected function clearPersistentData($key) 
	{
		if ( ! in_array($key, self::$kSupportedKeys)) 
		{
			self::errorLog('Unsupported key passed to clearPersistentData.');
			return;
		}

		$session_var_name = $this->constructSessionVariableName($key);
		unset($_SESSION[$session_var_name]);
	}

	protected function clearAllPersistentData() 
	{
		foreach (self::$kSupportedKeys as $key) 
		{
			$this->clearPersistentData($key);
		}
	}

	protected function constructSessionVariableName($key) 
	{
		return implode('_', array('fb',$this->getAppId(),$key));
	}
}

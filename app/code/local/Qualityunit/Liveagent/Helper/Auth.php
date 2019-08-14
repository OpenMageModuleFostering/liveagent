<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Juraj Simon
 *   @package WpLiveAgentPlugin
 *   @version 1.0.0
 *
 *   Licensed under GPL2
 */

class Qualityunit_Liveagent_Helper_Auth extends Qualityunit_Liveagent_Helper_Base {
	public function ping($url = null) {
		$url = $this->getRemoteApiUrl($url);
		$request = new La_Rpc_DataRequest("Gpf_Common_ConnectionUtil", "ping");
		$request->setUrl($url);
		try {
			$request->sendNow();
		} catch (Exception $e) {
			$this->_log('Unable to ping Live Agent remotelly' . $e->getMessage());
			throw new Qualityunit_Liveagent_Exception_ConnectProblem($e->getMessage());
		}
		$data = $request->getData();
		if ($data->getParam('status') != 'OK') {
			throw new Qualityunit_Liveagent_Exception_ConnectProblem($e->getMessage());
		}
	}
	
	public function getauthTokenByApi($url, $apiKey) {
	    $url = $this->getRemoteApiUrl($url);
	    $url .= '?handler=agents/' . urlencode($this->settingsModel->getOwnerEmail()) . '&apikey=' . $apiKey;
	    
	    $ch = curl_init();
	    curl_setopt($ch,CURLOPT_URL, $url);
	    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
	    $this->_log('Fetching user auth info from api url: ' . $url);
	    $curl_response=curl_exec($ch);
	    if ($curl_response === false) {
	        $info = curl_getinfo($ch);
	        curl_close($ch);
	        $this->_log('Error calling API request: ' . var_export($info, true));
	        return '';
	    }
	    $info = curl_getinfo($ch);
	    curl_close($ch);
	    $decodedResponse = json_decode($curl_response);
	    if (!isset($decodedResponse->response)) {
	        $this->_log('Error decoding API response: ' . $curl_response . ', info: ' . var_export($info, true));
	        return '';
	    }
	    if ($info['http_code'] != 200) {
	        $this->_log('API response returned error: ' . $curl_response . ', info: ' . var_export($info, true));
	        return '';
	    }
	    return $decodedResponse->response->authtoken;
	}
	
	/**
	 * @return La_Rpc_Data
	 */
	public function LoginAndGetLoginData($url = null, $username = null, $password = null) {
		$url = $this->getRemoteApiUrl($url);
		
		$request = new La_Rpc_DataRequest("Gpf_Api_AuthService", "authenticate");

		if ($username == null) {
			$request->setField('username' ,$this->settingsModel->getOwnerEmail());
		} else {
			$request->setField('username' ,$username);
		}
		
		if ($password == null) {
			$request->setField('password' ,$this->settingsModel->getOwnerPassword());
		} else {
			$request->setField('password' ,$password);
		}
		$request->setUrl($url);
		try {
			$request->sendNow();
		} catch (Exception $e) {
			$this->_log('Unable to login.');
			//$this->_log($e->getMessage());
			throw new Qualityunit_Liveagent_Exception_ConnectProblem();
		}
		if ($request->getData()->getParam('error')!=null) {
			$this->_log('Answer from server: ' . print_r($request->getResponseObject()->toObject(), true));
			throw new Qualityunit_Liveagent_Exception_ConnectProblem();
		}
		return $request->getData();
	}
}


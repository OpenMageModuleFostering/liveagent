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
?>
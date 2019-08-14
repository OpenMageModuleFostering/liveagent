<?php

class Qualityunit_Liveagent_Helper_Account extends Mage_Core_Helper_Abstract {
	public static function isOnline() {
		$auth = new Qualityunit_Liveagent_Helper_Auth();
		try {
			$auth->ping();
			Mage::log('Account is online!', Zend_Log::INFO);
			return true;
		} catch (Qualityunit_Liveagent_Exception_ConnectProblem $e) {
			Mage::log('Account not online', Zend_Log::INFO);
			return false;
		}
	}
}
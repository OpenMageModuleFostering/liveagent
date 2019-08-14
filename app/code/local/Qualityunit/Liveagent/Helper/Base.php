<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Juraj Simon
 *   @package WpLiveAgentPlugin
 *   @version 1.0.0
 *
 *   Licensed under GPL2
 */

class Qualityunit_Liveagent_Helper_Base {
	/**
	 * @var Qualityunit_Liveagent_Helper_Settings
	 */
	protected $settingsModel;
	const ACCOUNT_STATUS_NOTSET = 'N';
	const ACCOUNT_STATUS_SET = 'S';
	const ACCOUNT_STATUS_WAIT = 'W';

	public function _log($message) {
		Mage::log($message, Zend_Log::DEBUG);
	}

	public function __construct() {
		$this->settingsModel = new Qualityunit_Liveagent_Helper_Settings();
	}

	public function showAdminError($error) {
		Mage::log($message, Zend_Log::ERR);
	}

	public function showConnectionError() {
		$this->showAdminError(Mage::helper('liveagent')->__('Unable to connect to Live Agent. please check your connection settings'));
	}

	public function getRemoteTrackJsUrl() {
		return $this->settingsModel->getOption(Qualityunit_Liveagent_Helper_Settings::LA_URL_SETTING_NAME) . '/scripts/trackjs.php';
	}

	public function getRemotePixUrl() {
		return $this->settingsModel->getOption(Qualityunit_Liveagent_Helper_Settings::LA_URL_SETTING_NAME) . '/scripts/pix.gif';
	}

	public function getRemoteApiUrl($url = null) {
		if ($url == null) {
			return $this->settingsModel->getOption(Qualityunit_Liveagent_Helper_Settings::LA_URL_SETTING_NAME) . '/api/index.php';
		} else {
			return $url . '/api/index.php';
		}
	}

	protected function isEmpty($var) {
		return $var=== null || $var=='';
	}
}
?>
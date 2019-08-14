<?php

class Qualityunit_Liveagent_Adminhtml_LiveagentController extends Mage_Adminhtml_Controller_Action {

	const LIVEAGENT_PLUGIN_NAME = 'liveagent';
	const ACTION_CREATE = 'c';

	/**
	 * @var Qualityunit_Liveagent_Helper_Settings
	 **/
	private $settings = null;

	private function includePhpApi() {
		$path = dirname(__FILE__);
		if (file_exists($path . '/../../Helper/PhpApi.php')) {
			include_once $path . '/../../Helper/PhpApi.php';
			return;
		}
		include_once ('Qualityunit_Liveagent_Helper_PhpApi.php');
	}

	protected function _construct() {
		$this->includePhpApi();
		Qualityunit_Liveagent_Helper_Data::convertOldButton();
		parent::_construct();
		$this->settings = new Qualityunit_Liveagent_Helper_Settings();
	}



	private function initLayout() {
		$this->loadLayout()
		->_setActiveMenu('liveagent/account')
		->_addBreadcrumb(Mage::helper('adminhtml')->__('Account'), Mage::helper('adminhtml')->__('Settings'));
	}

	private function doAfterPost($params = array()) {
		$this->_redirect('*/*', $params);
	}

	private function doPostAction() {
		$post = $this->getRequest()->getPost();
		if (!array_key_exists('action', $post)) {
			$this->doAfterPost();
			return;
		}
		if ($post['action']=="r") {
			try {
				$this->validateSignupMail($post[Qualityunit_Liveagent_Helper_Settings::LA_OWNER_EMAIL_SETTING_NAME]);
				$this->validateSubdomain($post[Qualityunit_Liveagent_Helper_Settings::LA_URL_SETTING_NAME]);
				$this->registerAccountAction(
						$post[Qualityunit_Liveagent_Block_Signup::FULL_NAME_FIELD],
						$post[Qualityunit_Liveagent_Helper_Settings::LA_OWNER_EMAIL_SETTING_NAME],
						$post[Qualityunit_Liveagent_Helper_Settings::LA_URL_SETTING_NAME],
						md5(rand(90000,99999) . microtime() . rand(90000,99999)),
						''
				);
			} catch (Qualityunit_Liveagent_Exception_SignupFailed $e) {
				$this->signupFailed($e);
				return;
			}
			$this->doAfterPost();
			return;
		}
		if ($post['action']==Qualityunit_Liveagent_Block_Account::SAVE_ACCOUNT_SETTINGS_ACTION_FLAG) {
			if (!$this->checkAccountSettings($post)) {
				$this->doAfterPost($this->getAccountSettingsPost($post));
				return;
			}
			$auth = new Qualityunit_Liveagent_Helper_Auth();
			try {
				$auth->ping($post[Qualityunit_Liveagent_Helper_Settings::LA_URL_SETTING_NAME]);
			} catch (Qualityunit_Liveagent_Exception_ConnectProblem $e) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to connect to LiveAgent at') . ' ' . $post[Qualityunit_Liveagent_Helper_Settings::LA_URL_SETTING_NAME] . '. ' . $this->getAccountSettingsChangeRevertLink());
				$this->doAfterPost($this->getAccountSettingsPost($post));
				return;
			}
			if ($this->settings->useApiKey()) {
			    try {
			        $token = $auth->getauthTokenByApi($post[Qualityunit_Liveagent_Helper_Settings::LA_URL_SETTING_NAME], $post[Qualityunit_Liveagent_Helper_Settings::LA_OWNER_PASSWORD_SETTING_NAME]);
			        $this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::OWNER_AUTHTOKEN, $token);
			    } catch (Qualityunit_Liveagent_Exception_ConnectProblem $e) {
			    }
			    if ($token == null) {
			        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to login to LiveAgent with given credentails. Please check your url, username and api key.') . ' ' . $this->getAccountSettingsChangeRevertLink());
			        $this->doAfterPost($this->getAccountSettingsPost($post));
			        return;
			    }
			} else {
    			try {
    				$auth->LoginAndGetLoginData($post[Qualityunit_Liveagent_Helper_Settings::LA_URL_SETTING_NAME],
    						$post[Qualityunit_Liveagent_Helper_Settings::LA_OWNER_EMAIL_SETTING_NAME],
    						$post[Qualityunit_Liveagent_Helper_Settings::LA_OWNER_PASSWORD_SETTING_NAME]);
    			} catch (Qualityunit_Liveagent_Exception_ConnectProblem $e) {
    				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to login to LiveAgent with given credentails. Please check your username and password.') . ' ' . $this->getAccountSettingsChangeRevertLink());
    				$this->doAfterPost($this->getAccountSettingsPost($post));
    				return;
    			}
			}
			
			$this->saveAccountSettings($post, $token);
			if (!$this->isSetStatus()) {
				$this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::ACCOUNT_STATUS, Qualityunit_Liveagent_Helper_Base::ACCOUNT_STATUS_SET);
			}
			Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Account settings were saved successfully'));
		}
		if ($post['action']==Qualityunit_Liveagent_Block_Buttoncode::SAVE_BUTTON_CODE_ACTION_FLAG) {
			$this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::BUTTON_CODE,
					html_entity_decode($post[Qualityunit_Liveagent_Helper_Settings::BUTTON_CODE]));
			Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Button code was saved successfully'));
		}
		$this->doAfterPost();
	}
	
	private function getAccountSettingsChangeRevertLink() {
		return '<a href="'.$this->getUrl('*/*').'">' . Mage::helper('adminhtml')->__('Undo this change') . '</a>';
	}

	private function getAccountSettingsPost($post) {
		return array(
				Qualityunit_Liveagent_Helper_Settings::LA_URL_SETTING_NAME => ' ' . trim(base64_encode($post[Qualityunit_Liveagent_Helper_Settings::LA_URL_SETTING_NAME])),
				Qualityunit_Liveagent_Helper_Settings::LA_OWNER_EMAIL_SETTING_NAME => ' ' . trim(base64_encode($post[Qualityunit_Liveagent_Helper_Settings::LA_OWNER_EMAIL_SETTING_NAME])),
				Qualityunit_Liveagent_Helper_Settings::LA_OWNER_PASSWORD_SETTING_NAME => ' ' . trim(base64_encode($post[Qualityunit_Liveagent_Helper_Settings::LA_OWNER_PASSWORD_SETTING_NAME])),
				'action' => Qualityunit_Liveagent_Block_Account::CHANGE_ACCOUNT_ACTION_FLAG
				);
	}

	private function validateSubdomain($subdomain) {
	    if (0 == preg_match('/^[a-z0-9-]{3,}$/', $subdomain)) {
	        throw new Qualityunit_Liveagent_Exception_SignupFailed(Mage::helper('adminhtml')->__('Please enter valid subdomain name'));
	    }
	}
	
	private function validateSignupMail($mail) {
		if (!Zend_Validate::is($mail, 'EmailAddress')) {
			throw new Qualityunit_Liveagent_Exception_SignupFailed(Mage::helper('adminhtml')->__('Please enter valid email address'));
		}
	}

	private function signupFailed($e) {
		Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		$this->doAfterPost();
	}

	public function postAction() {
		$this->initLayout();
		try {
			$this->doPostAction();
		} catch (Exception $e) {
			Mage::log($e->getMessage(), Zend_log::ERR);
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			$this->_redirect('*/*');
			$this->renderLayout();
		}
	}

	private function checkAccountSettings($post) {
		if (trim($post[Qualityunit_Liveagent_Helper_Settings::LA_OWNER_EMAIL_SETTING_NAME]) == null) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Username can not be empty.') . ' ' . $this->getAccountSettingsChangeRevertLink());
			return false;
		}
		if (trim($post[Qualityunit_Liveagent_Helper_Settings::LA_OWNER_PASSWORD_SETTING_NAME]) == null) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Password can not be empty.') . ' ' . $this->getAccountSettingsChangeRevertLink());
			return false;
		}
		if (trim($post[Qualityunit_Liveagent_Helper_Settings::LA_URL_SETTING_NAME]) == null) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Url can not be empty.') . ' ' . $this->getAccountSettingsChangeRevertLink());
			return false;
		}
		if (strpos($post[Qualityunit_Liveagent_Helper_Settings::LA_OWNER_EMAIL_SETTING_NAME], '@') === false) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Username must be valid email.') . ' ' . $this->getAccountSettingsChangeRevertLink());
			return false;
		}
		return true;
	}

	private function saveAccountsettings($post, $token = null) {
		$oldUrl = $this->settings->getOption(Qualityunit_Liveagent_Helper_Settings::LA_URL_SETTING_NAME);
		$this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::LA_OWNER_EMAIL_SETTING_NAME, $post[Qualityunit_Liveagent_Helper_Settings::LA_OWNER_EMAIL_SETTING_NAME]);
		$this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::LA_OWNER_PASSWORD_SETTING_NAME, $post[Qualityunit_Liveagent_Helper_Settings::LA_OWNER_PASSWORD_SETTING_NAME]);
		$this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::LA_URL_SETTING_NAME, $post[Qualityunit_Liveagent_Helper_Settings::LA_URL_SETTING_NAME]);
		if ($this->settings->useApiKey() && $token != '') {
		    $this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::OWNER_AUTHTOKEN. $token);
		} else {
		    $this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::OWNER_AUTHTOKEN, '');
		}
		$this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::OWNER_SESSIONID, '');
		if ($oldUrl == $post[Qualityunit_Liveagent_Helper_Settings::LA_URL_SETTING_NAME]) {
			return;
		}
		$this->settings->saveDefaultButtonCode();
	}

	public function indexAction() {
		$this->initLayout();
		try {
			$this->doIndexAction();
		} catch (Exception $e) {
			Mage::log($e->getMessage(), Zend_log::ERR);
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			$this->renderLayout();
		}
	}

	private function getAccountNotReachableTimes() {
		$times = $this->settings->getOption(Qualityunit_Liveagent_Helper_Settings::ACCOUNT_NOT_REACHABLE_TIMES);
		if ($times == '') {
			return 0;
		}
		return $times;
	}

	/**
	 * @return Qualityunit_Liveagent_Block_Waiting
	 */
	private function getWaitingDialog() {
		$this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::ACCOUNT_NOT_REACHABLE_TIMES, $this->settings->getOption(Qualityunit_Liveagent_Helper_Settings::ACCOUNT_NOT_REACHABLE_TIMES) + 1);
		return new Qualityunit_Liveagent_Block_Waiting();
	}

	public function doIndexAction() {
		if ($this->getRequest()->getParam('action')=="") {
			if ($this->isNew()) {
				$block = new Qualityunit_Liveagent_Block_Signup();
			} else if ($this->isWaiting()) {
				if ($this->getAccountNotReachableTimes() <= 1) {
					$block = $this->getWaitingDialog();
				} else if ($this->getAccountNotReachableTimes() > 1) {
					if (Qualityunit_Liveagent_Helper_Account::isOnline() || $this->getAccountNotReachableTimes() > 3) {
						$this->skipWaitAction();
						$this->renderAccountDialog();
						$this->renderLayout();
						return;
					}
					$block = $this->getWaitingDialog();
				}
			} else {
				$this->renderAccountDialog();
				$this->renderLayout();
				return;
			}
			$this->_addContent($this->getLayout()->createBlock($block));
			$this->renderLayout();
			return;
		}
		if ($this->getRequest()->getParam('action')==Qualityunit_Liveagent_Block_Account::CANCEL_ACCOUNT_ACTION_FLAG) {
			$this->resetAccountAction();
			$block = new Qualityunit_Liveagent_Block_Signup();
		}
 		if ($this->getRequest()->getParam('action') == Qualityunit_Liveagent_Block_Account::CHANGE_ACCOUNT_ACTION_FLAG) {
 			$block = new Qualityunit_Liveagent_Block_Account();
 		}
		if ($this->getRequest()->getParam('action')=="s") {
			$this->skipWaitAction();
			$this->renderAccountDialog();
			$this->renderLayout();
			return;
		}
		$this->_addContent($this->getLayout()->createBlock($block));
		$this->renderLayout();
	}
	
	private function checkButtonCodeIsEmpty() {
		if  ($this->settings->getOption(Qualityunit_Liveagent_Helper_Settings::BUTTON_CODE) == null) {
			Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('adminhtml')->__('The chat button is currently disabled.'));
		}
	}

	private function renderAccountDialog() {
		if ($this->isSetStatus() && Qualityunit_Liveagent_Helper_Account::isOnline()) {
			$this->checkButtonCodeIsEmpty();
			$block = new Qualityunit_Liveagent_Block_Buttoncode();
		}
		if ($this->isSetStatus() && !Qualityunit_Liveagent_Helper_Account::isOnline()) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to connect LiveAgent at') . ' ' . $this->settings->getLiveAgentUrl());
			$block = new Qualityunit_Liveagent_Block_Account();
		}
		if (!$this->isSetStatus()) {
			$block = new Qualityunit_Liveagent_Block_Account();
		}
		$this->_addContent($this->getLayout()->createBlock($block));
	}

	private function isNew() {
		return $this->settings->getAccountStatus() == Qualityunit_Liveagent_Helper_Base::ACCOUNT_STATUS_NOTSET;
	}

	private function isSetStatus() {
		return $this->settings->getAccountStatus() == Qualityunit_Liveagent_Helper_Base::ACCOUNT_STATUS_SET;
	}

	private function isWaiting() {
		return $this->settings->getAccountStatus() == Qualityunit_Liveagent_Helper_Base::ACCOUNT_STATUS_WAIT;
	}

	private function resetAccountAction() {
	    $this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::LA_USE_API_KEY, 'Y');
		$this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::ACCOUNT_STATUS, Qualityunit_Liveagent_Helper_Base::ACCOUNT_STATUS_NOTSET);
	}

	private function sendSignupRequest($name, $email, $domain, $password, $papvisitorId) {
		$signupHelper = new Qualityunit_Liveagent_Helper_Signup();
		try {
			$response = $signupHelper->signup($name, $email, $domain, $password, $papvisitorId);
		} catch (Qualityunit_Liveagent_Exception_Base $e) {
			throw new Qualityunit_Liveagent_Exception_SignupFailed($e->getMessage());
		}
		Mage::log("Signup response recieved: " . print_r($response, true), Zend_log::DEBUG);
		
// 		if ($response->success != "Y") {
// 			Mage::log("Response contain error:" . $response->errorMessage, Zend_log::DEBUG);
// 			throw new Qualityunit_Liveagent_Exception_SignupFailed($response->errorMessage);
// 		}
		Mage::log("Response OK", Zend_log::DEBUG);
	}

	private function registerAccountAction($name, $email, $domain, $password, $papvisitorId) {
		$this->sendSignupRequest($name, $email, $domain, $password, $papvisitorId);
		$this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::LA_URL_SETTING_NAME, 'http://' . $domain . '.ladesk.com');
		$this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::LA_OWNER_EMAIL_SETTING_NAME, $email);
		$this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::LA_OWNER_PASSWORD_SETTING_NAME, $password);
		$this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::LA_USE_API_KEY, 'Y');
		$this->settings->saveDefaultButtonCode();
		$this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::ACCOUNT_STATUS, Qualityunit_Liveagent_Helper_Base::ACCOUNT_STATUS_WAIT);
	}

	private function skipWaitAction() {
		$this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::ACCOUNT_STATUS, Qualityunit_Liveagent_Helper_Base::ACCOUNT_STATUS_SET);
		$this->settings->setOption(Qualityunit_Liveagent_Helper_Settings::ACCOUNT_NOT_REACHABLE_TIMES, 0);
	}
}

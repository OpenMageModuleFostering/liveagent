<?php

class Qualityunit_Liveagent_Model_Settings extends Mage_Core_Model_Abstract {
	public function _construct() {
		parent::_construct();
		$this->_init('liveagent/settings');
	}

	public function replacePlaceholders($htmlCode) {
		$customerSession = Mage::getSingleton('customer/session');
		if (!$customerSession->isLoggedIn()) {
			$htmlCode = str_replace('%%firstName%%', '', $htmlCode);
			$htmlCode = str_replace('%%lastName%%', '', $htmlCode);
			$htmlCode = str_replace('%%email%%', '', $htmlCode);
			$htmlCode = str_replace('%%phone%%', '', $htmlCode);
			$htmlCode = str_replace('%%order%%', '', $htmlCode);
			return $htmlCode;
		}

		$customer = $customerSession->getCustomer();

		if (($customer->getFirstname() != null) && ($customer->getFirstname() != '')) {

			$htmlCode = str_replace('%%firstName%%', "LiveAgent.addUserDetail('firstName', '" . $customer->getFirstname() . "');\n", $htmlCode);
		}
		else {
			$htmlCode = str_replace('%%firstName%%', '', $htmlCode);
		}

		if (($customer->getLastname() != null) && ($customer->getLastname() != '')) {
			$htmlCode = str_replace('%%lastName%%', "LiveAgent.addUserDetail('lastName', '" . $customer->getLastname() . "');\n", $htmlCode);
		}
		else {
			$htmlCode = str_replace('%%lastName%%', '', $htmlCode);
		}

		if (($customer->getEmail() != null) && ($customer->getEmail() != '')) {
			$htmlCode = str_replace('%%email%%', "LiveAgent.addUserDetail('email', '" . $customer->getEmail() . "');\n", $htmlCode);
		}
		else {
			$htmlCode = str_replace('%%email%%', '', $htmlCode);
		}

    try {
  		if (($customer->getPrimaryBillingAddress()->getTelephone() != null) && ($customer->getPrimaryBillingAddress()->getTelephone() != '')) {
  			$htmlCode = str_replace('%%phone%%', "LiveAgent.addUserDetail('phone', '" . $customer->getPrimaryBillingAddress()->getTelephone() . "');\n", $htmlCode);
  		}
  		else {
  			$htmlCode = str_replace('%%phone%%', '', $htmlCode);
  		}
    }
    catch (Exception $e) {
  			$htmlCode = str_replace('%%phone%%', '', $htmlCode);
    }
		return $htmlCode;
	}

	public function getChatsOverview() {
		$settings = new Qualityunit_Liveagent_Helper_Settings();
		if ($settings->getApiKey() == '' || $settings->getLiveAgentUrl() == '') {
			return array('error' => 'Your LiveAgent module is not configured yet. When the configuration is set correctly, you will see some basic reports here.');
		}
		$connect = new Qualityunit_Liveagent_Helper_Connect();
		try {
			$result = $connect->getOverview($settings->getLiveAgentUrl(), $settings->getApiKey());
			$overviews = array('error' => '',
				'visitors' => $result->visitors,
				'chats' => $result->chats,
				'queue' => $result->queue,
				'agents' => $result->agents
			);
		} catch (Qualityunit_Liveagent_Exception_ConnectFailed $e) {
			$overviews = array('error' => 'Error occurred: ' . $e->getMessage());
		}
		return $overviews;
	}
}
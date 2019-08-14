<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Juraj Simon
 *   @package WpLiveAgentPlugin
 *   @version 1.0.0
 *
 *   Licensed under GPL2
 */

class Qualityunit_Liveagent_Helper_Settings {
	const CACHE_VALIDITY = 600;

	//internal settings
	const INTERNAL_SETTINGS = 'la-settings_internal-settings';
	const OWNER_SESSIONID = 'la-settings_owner-sessionid';
	const OWNER_AUTHTOKEN = 'la-settings_owner-authtoken';
	const ACCOUNT_STATUS = 'la-settings_accountstatus';
	const ACCOUNT_NOT_REACHABLE_TIMES = 'la-settings_accountnotreachabletimes';


	//general page
	const GENERAL_SETTINGS_PAGE_NAME = 'la-config-general-page';
	const SIGNUP_SETTINGS_PAGE_NAME = 'la-config-signup-page';
	const SIGNUP_WAIT_SETTINGS_PAGE_NAME = 'la-config-signup-wait-page';

	const LA_URL_SETTING_NAME = 'la-url';
	const LA_OWNER_EMAIL_SETTING_NAME = 'la-owner-email';
	const LA_USE_API_KEY = 'la-use-api-key';
	const LA_OWNER_PASSWORD_SETTING_NAME = 'la-owner-password';
	const GENERAL_SETTINGS_PAGE_STATE_SETTING_NAME = 'general-settings-state';

	//buttons options
	const BUTTON_CODE = 'la-config-button-code';

	const NO_AUTH_TOKEN = 'no_auth_token';

	const ACCOUNT_STATUS_NOTSET = 'N';
	const ACCOUNT_STATUS_SET = 'S';

	public function sanitizeUrl($url) {
		if (stripos($url, 'http://')!==false || stripos($url, 'https://')!==false) {
			return $url;
		}
		return 'http://' . $url;
	}

	public function getOwnerSessionId() {
		return $this->getOption(Qualityunit_Liveagent_Helper_Settings::OWNER_SESSIONID);
	}

	public function getOwnerAuthToken() {
		$token = $this->getOption(Qualityunit_Liveagent_Helper_Settings::OWNER_AUTHTOKEN);
		if ($token == '') {
			$this->login();
			$token = $this->getOption(Qualityunit_Liveagent_Helper_Settings::OWNER_AUTHTOKEN);
		}
		return $token;
	}

	public function login() {
		$auth = new Qualityunit_Liveagent_Helper_Auth();
	    if ($this->getOption(Qualityunit_Liveagent_Helper_Settings::LA_USE_API_KEY) == 'Y') {
	        //use api and fetch auth token
	        $token = $auth->getauthTokenByApi(null , $this->getOwnerPassword());
	        $this->setOption(Qualityunit_Liveagent_Helper_Settings::OWNER_AUTHTOKEN, $token);
	        return $token;
	    }
	    
		
	    $loginData = $auth->LoginAndGetLoginData();
		try {
		 $sessionId = $loginData->getValue('session');
		 $this->setOption(Qualityunit_Liveagent_Helper_Settings::OWNER_SESSIONID, $sessionId);
		} catch (La_Data_RecordSetNoRowException $e) {
		 throw new Qualityunit_Liveagent_Exception_ConnectProblem();
		}
		try {
		 $token = $loginData->getValue('authtoken');
		 $sessionId = $token;
		 $this->setOption(Qualityunit_Liveagent_Helper_Settings::OWNER_AUTHTOKEN, $token);
		} catch (La_Data_RecordSetNoRowException $e) {
		 // we are communicating with older LA that does not send auth token
		 $this->setOption(Qualityunit_Liveagent_Helper_Settings::OWNER_AUTHTOKEN, '');
		}
		return $sessionId;
	}

	public function getLiveAgentUrl() {
		$url = $this->getOption(Qualityunit_Liveagent_Helper_Settings::LA_URL_SETTING_NAME);
		if ($url == null) {
			return $url;
		}
		if (strpos($url, 'http://') === false && strpos($url, 'https://') === false) {
			return 'http://' . $url;
		}
		return $url;
	}

	public function useApiKey() {
	    return $this->getOption(Qualityunit_Liveagent_Helper_Settings::LA_USE_API_KEY) == 'Y';
	}
	
	public function getOwnerEmail() {
		return $this->getOption(Qualityunit_Liveagent_Helper_Settings::LA_OWNER_EMAIL_SETTING_NAME);
	}

	public function getOwnerPassword() {
		return $this->getOption(Qualityunit_Liveagent_Helper_Settings::LA_OWNER_PASSWORD_SETTING_NAME);
	}

	public function setOption($name, $value) {
		$setting = Mage::getModel('liveagent/settings')->load($name, 'name');
		$setting->setValue($value);
		$setting->setName($name);
		$setting->save();
	}

	public function saveDefaultButtonCode() {
		$this->saveButtonCodeForButtonId('button1');
	}

	public function saveButtonCodeForButtonId($buttonid) {
		$url = $this->getLiveAgentUrl();
		$this->setOption(self::BUTTON_CODE, "
				<script type=\"text/javascript\">
(function(d, src, c) { var t=d.scripts[d.scripts.length - 1],s=d.createElement('script');s.id='la_x2s6df8d';s.async=true;s.src=src;s.onload=s.onreadystatechange=function(){var rs=this.readyState;if(rs&&(rs!='complete')&&(rs!='loaded')){return;}c(this);};t.parentElement.insertBefore(s,t.nextSibling);})(document,
'//latrialtst456466.ladesk.com/scripts/track.js',
function(e){});
</script>
				");
	}

	public function getOption($name) {
		$setting = Mage::getModel('liveagent/settings')->load($name, 'name');
		return $setting->getData('value');
	}

	public function getAccountStatus() {
		return $this->getOption(Qualityunit_Liveagent_Helper_Settings::ACCOUNT_STATUS);
	}

	public function connectionSettingsAreEmpty() {
		return $this->getOption(Qualityunit_Liveagent_Helper_Settings::LA_URL_SETTING_NAME) == '' && $this->getOption(Qualityunit_Liveagent_Helper_Settings::LA_OWNER_EMAIL_SETTING_NAME) == '';
	}
}


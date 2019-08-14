<?php

class Qualityunit_Liveagent_Block_Account extends Qualityunit_Liveagent_Block_Base {

	const SAVE_ACCOUNT_SETTINGS_ACTION_FLAG = 'sas';
	const CANCEL_ACCOUNT_ACTION_FLAG = 'c';
	const CREATE_ACCOUNT_ACTION_FLAF = 's';
	const CHANGE_ACCOUNT_ACTION_FLAG = 'h';

	public function _prepareLayout() {
		parent::_prepareLayout();
		$settings = new Qualityunit_Liveagent_Helper_Settings();
		$this->assignVariable('dialogCaption', Mage::helper('adminhtml')->__('LiveAgent - Free live chat and helpdesk plugin for Magento'));
		$this->assignVariable('submitCaption', Mage::helper('adminhtml')->__('Save Account Settings'));
		$this->assignVariable('settingsSection', Mage::helper('adminhtml')->__('Account Settings'));
		$this->assignVariable('beforeDeleteQuestion', Mage::helper('adminhtml')->__('Are you sure you want to cancel your account?'));
		$this->assignVariable('formKey', Mage::getSingleton('core/session')->getFormKey());
		$this->assignVariable('saveUrlAction', $this->getUrl('*/*/post'));
		$this->assignVariable('cancelUrlAction', $this->getUrl('*/*/index', array('key' => $this->getRequest()->get('key'), 'action' => self::CANCEL_ACCOUNT_ACTION_FLAG)));
		$this->assignVariable('urlLabel', Mage::helper('adminhtml')->__('Url')) . ':';
		$this->assignVariable('urlHelp', Mage::helper('adminhtml')->__('Url where your LiveAgent installation is located'));
		$this->assignVariable('laOwnerEmailHelp', Mage::helper('adminhtml')->__('Username which you use to login to your Live Agnet'));
		$this->assignVariable('nameLabel', Mage::helper('adminhtml')->__('Username')) . ':';
	    if ($settings->useApiKey()) {
	        $this->assignVariable('passwordLabel', Mage::helper('adminhtml')->__('Api key')) . ':';
	        $this->assignVariable('laOwnerPasswordHelp', Mage::helper('adminhtml')->__('Your REST api key'));
	    } else {
		  $this->assignVariable('passwordLabel', Mage::helper('adminhtml')->__('Password')) . ':';
		  $this->assignVariable('laOwnerPasswordHelp', Mage::helper('adminhtml')->__('Your password'));
	    }
		
		$this->assignVariable('cancelLink', Mage::helper('adminhtml')->__('Cancel account'));
		$this->assignVariable('cancelHelp', Mage::helper('adminhtml')->__('this will clear all your existing account settings and offer you to create a new free account'));
		$this->assignVariable('contactLink', Mage::helper('adminhtml')->__('contact us'));
		$this->assignVariable('contactHelp', Mage::helper('adminhtml')->__('Do you need any help with this plugin? Feel free to'));
		$this->assignVariable(Qualityunit_Liveagent_Helper_Settings::LA_URL_SETTING_NAME, $this->getInputBox(Qualityunit_Liveagent_Helper_Settings::LA_URL_SETTING_NAME, ''));
		$this->assignVariable(Qualityunit_Liveagent_Helper_Settings::LA_OWNER_EMAIL_SETTING_NAME, $this->getInputBox(Qualityunit_Liveagent_Helper_Settings::LA_OWNER_EMAIL_SETTING_NAME, ''));
		$this->assignVariable(Qualityunit_Liveagent_Helper_Settings::LA_OWNER_PASSWORD_SETTING_NAME, $this->getPasswordBox(Qualityunit_Liveagent_Helper_Settings::LA_OWNER_PASSWORD_SETTING_NAME, ''));
		$this->assignVariable('saveActionSettingsFlag', self::SAVE_ACCOUNT_SETTINGS_ACTION_FLAG);
	}

	protected function getPasswordBox($name, $value) {
		$params = $this->getRequest()->getParams();
		if (isset($params[$name])) {
			return parent::getPasswordBox($name, base64_decode(trim($this->getRequest()->getParam($name))));
		}
		$settings = new Qualityunit_Liveagent_Helper_Settings();
		return parent::getPasswordBox($name, $settings->getOption($name));
	}

	protected function getInputBox($name, $value) {
		$params = $this->getRequest()->getParams();
		if (isset($params[$name])) {
			return parent::getInputBox($name, base64_decode(trim($this->getRequest()->getParam($name))));
		}
		$settings = new Qualityunit_Liveagent_Helper_Settings();
		return parent::getInputBox($name, $settings->getOption($name));
	}

	protected function getTemplateString() {
		return '
		<script type="text/javascript">
		var cancelMyAccount = function() {
		if (confirm(\'{beforeDeleteQuestion}\')) {
		setLocation(\'{cancelUrlAction}\')
	}
	}
	</script>
	<form id="configForm" name="edit_form" action="{saveUrlAction}" method="post">
	<input name="form_key" type="hidden" value="{formKey}" />
	<input name="action" type="hidden" value="{saveActionSettingsFlag}"/>
	<div class="content-header">
	<table cellspacing="0">
	<tbody><tr>
	<td style="width:50%;"><h3 class="icon-head head-promo-catalog">{dialogCaption}</h3></td>
	<td class="form-buttons"><button id="id_5806f3a306fa79f4340cb58c1d190ff5" type="button" class="scalable save" onclick="configForm.submit()" style=""><span>{submitCaption}</span></button></td>
	</tr>
	</tbody>
	</table>
	</div>
	<div class="entry-edit">
	<fieldset>
	{contactHelp}&nbsp;<a href="http://support.qualityunit.com/submit_ticket" target="_blank">{contactLink}</a>.
	</fieldset>
	</div>
	<div class="entry-edit">
	<div class="entry-edit-head"><h4>{settingsSection}</h4></div>
	<fieldset>
	<table cellspacing="0" class="form-list">
	<colgroup class="label"></colgroup>
	<colgroup class="value"></colgroup>
	<colgroup class="scope-label"></colgroup>
	<colgroup class=""></colgroup>
	<tbody>
	<tr id="row_la_url">
	<td class="label"><label for="row_la_url">{urlLabel}</label></td>
	<td class="value">{la-url}
	<p class="note"><span>{urlHelp}</span></p></td>
	<td class="scope-label"></td><td></td>
	</tr>
	<tr id="row_owner_email">
	<td class="label"><label for="row_owner_email">{nameLabel}</label></td>
	<td class="value">{la-owner-email}
	<p class="note"><span>{laOwnerEmailHelp}</span></p></td>
	<td class="scope-label"></td><td></td>
	</tr>
	<tr id="row_owner_pass">
	<td class="label"><label for="row_owner_pass">{passwordLabel}</label></td>
	<td class="value">{la-owner-password}
	<p class="note"><span>{laOwnerPasswordHelp}</span></p></td>
	<td class="scope-label"></td><td></td>
	</tr>
	<tr>
	<td colspan="4" class="scope-label"><button onclick="cancelMyAccount()" type="button" class="scalable delete"><span>{cancelLink}</span></button>&nbsp;&nbsp;&nbsp;&nbsp;{cancelHelp}</td>
	</tr>
	<tr>
	<td colspan="4" class="scope-label"></td>
	</tr>
	</tbody>
	</table>
	</fieldset>
	</div>
	';
	}
}

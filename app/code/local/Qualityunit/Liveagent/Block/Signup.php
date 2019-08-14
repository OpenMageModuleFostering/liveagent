<?php

class Qualityunit_Liveagent_Block_Signup extends Qualityunit_Liveagent_Block_Base {

	const FULL_NAME_FIELD = 'la-full-name';

	public function _prepareLayout() {
		parent::_prepareLayout();
		$this->assignVariable('dialogCaption', Mage::helper('adminhtml')->__('LiveAgent - Live chat and helpdesk plugin for Magento'));
		$this->assignVariable(self::FULL_NAME_FIELD, $this->getInputBox(self::FULL_NAME_FIELD, $this->getOwnerName()));
		$this->assignVariable(Qualityunit_Liveagent_Helper_Settings::LA_OWNER_EMAIL_SETTING_NAME, $this->getInputBox(Qualityunit_Liveagent_Helper_Settings::LA_OWNER_EMAIL_SETTING_NAME, $this->getOwnerEmail()));
		$this->assignVariable(Qualityunit_Liveagent_Helper_Settings::LA_URL_SETTING_NAME, $this->getInputBox(Qualityunit_Liveagent_Helper_Settings::LA_URL_SETTING_NAME, $this->getdomainOnly()));
		$this->assignVariable('submitCaption', Mage::helper('adminhtml')->__('Create your trial account'));
		$this->assignVariable('skipUrlAction', $this->getUrl('*/*/index', array('key' => $this->getRequest()->get('key'), 'action' => Qualityunit_Liveagent_Block_Account::CREATE_ACCOUNT_ACTION_FLAF)));
		$this->assignVariable('registerUrlAction', $this->getUrl('*/*/post'));
		$this->assignVariable('formKey', Mage::getSingleton('core/session')->getFormKey());
		$this->assignVariable('fullNameLabel', Mage::helper('adminhtml')->__('Full name'));
		$this->assignVariable('emailLabel', Mage::helper('adminhtml')->__('Email'));
		$this->assignVariable('fullNameHelp', Mage::helper('adminhtml')->__('Your first name and last name'));
		$this->assignVariable('urlLabel', Mage::helper('adminhtml')->__('Domain selection'));
		$this->assignVariable('urlHelp', Mage::helper('adminhtml')->__('.ladesk.com'));
		$this->assignVariable('termsLabel', Mage::helper('adminhtml')->__('By creating an account I agree to'));
		$this->assignVariable('termsLink', Mage::helper('adminhtml')->__('Terms & Conditions'));
		$this->assignVariable('skipLink', Mage::helper('adminhtml')->__('Skip this step, I already have an account'));
		$this->assignVariable('settingsSection', Mage::helper('adminhtml')->__('New account'));
		$this->assignVariable('pluginHelpText', Mage::helper('adminhtml')->__('We want you to enjoy the full functionality of LiveAgent with the trial account. It does not limit the number of agents and you have the option to activate the most of available features.'));
	}

	private function getdomainOnly() {
		$domain = preg_replace('/^(.*\.)?([^.]*\..*)$/', '$2', @$_SERVER['HTTP_HOST']);
		if (trim($domain) == 'localhost' || trim($domain) == 'www' || trim($domain) == 'http' || trim($domain) == 'dev') {
			return '';
		}
		return $domain;
	}

	private function getOwnerName() {
		try {
			$user = Mage::getSingleton('admin/session')->getUser();
			return $user->getFirstname() . ' ' . $user->getLastname();
		} catch (Exception $e) {
			Mage::log($e->getMessage(), Zend_Log::ERR);
			return '';
		}
	}

	private function getOwnerEmail() {
		try {
			$user = Mage::getSingleton('admin/session')->getUser();
			return $user->getEmail();
		} catch (Exception $e) {
			Mage::log($e->getMessage(), Zend_Log::ERR);
			return '';
		}
	}

	protected function getTemplateString() {
		return '
		<script type="text/javascript">
        	var skipCreate = function() {				
					setLocation(\'{skipUrlAction}\')
			}
		</script>
		<form id="configForm" name="edit_form" action="{registerUrlAction}" method="post">
		<input name="form_key" type="hidden" value="{formKey}" />
        <input name="action" type="hidden" value="r"/>
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
				{pluginHelpText}
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
            			<tr id="row_la_full_name">
            				<td class="label"><label for="row_la_full_name">{fullNameLabel}</label></td>
            				<td class="value">{la-full-name}
            				<p class="note"><span>{fullNameHelp}</span></p></td>
            				<td class="scope-label"></td><td></td>
            			</tr>
            			<tr id="row_owner_email">
            				<td class="label"><label for="row_owner_email">{emailLabel}</label></td>
            				<td class="value">{la-owner-email}</td>
            				<td class="scope-label"></td><td></td>
            			</tr>
            			<tr id="row_owner_pass">
            				<td class="label"><label for="row_owner_pass">{urlLabel}</label></td>
            				<td class="value">{la-url}</td>
            				<td class="scope-label">{urlHelp}</td><td></td>
            			</tr>
            			<tr>
            				<td colspan="4" class="scope-label">{termsLabel}&nbsp;<a target="_blank" href="http://www.qualityunit.com/liveagent/pricing/hosted/terms-and-conditions">{termsLink}</a>.</td>
            			</tr>
            			<tr>
            				<td colspan="4" class="scope-label"></td>
            			</tr>
            			<tr>            				
            				<td colspan="4" class="scope-label"><button onclick="skipCreate()" type="button" class="scalable "><span>{skipLink}</span></button></td>
            			</tr>
            		</tbody>
            	</table>
            </fieldset>
        </div>			
	';
	}
}
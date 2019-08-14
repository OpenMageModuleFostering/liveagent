<?php

class Qualityunit_Liveagent_Block_Buttoncode extends Qualityunit_Liveagent_Block_Base {

	const SAVE_BUTTON_CODE_ACTION_FLAG = 'sb';

	public function _prepareLayout() {
		$settings = new Qualityunit_Liveagent_Helper_Settings();
		parent::_prepareLayout();
		$this->assignVariable('dialogCaption', Mage::helper('adminhtml')->__('LiveAgent - Free live chat and helpdesk plugin for Magento'));
		$this->assignVariable('submitCaption', Mage::helper('adminhtml')->__('Save'));
		$this->assignVariable('formKey', Mage::getSingleton('core/session')->getFormKey());
		$this->assignVariable('saveUrlAction', $this->getUrl('*/*/post'));
		$settings = new Qualityunit_Liveagent_Helper_Settings();
		$code = htmlentities($settings->getOption(Qualityunit_Liveagent_Helper_Settings::BUTTON_CODE));
		$this->assignVariable('la-config-button-code', $this->getTextArea('la-config-button-code', $code, 10 ,100, ' textarea'));
		$this->assignVariable('buttonCodeLabel', Mage::helper('adminhtml')->__('Button code'));
		$this->assignVariable('buttonCodeHelp', Mage::helper('adminhtml')->__('Place here the code from your LiveAgent admin panel'));
		$this->assignVariable('saveButtonCodeFlag', self::SAVE_BUTTON_CODE_ACTION_FLAG);
		$this->assignVariable('accountSectionLabel', Mage::helper('adminhtml')->__('Your account'));
		$this->assignVariable('accountUrlLabel', Mage::helper('adminhtml')->__('Account url'));
		$this->assignVariable('la-url', $settings->getOption(Qualityunit_Liveagent_Helper_Settings::LA_URL_SETTING_NAME));
		$this->assignVariable('loginLabel', Mage::helper('adminhtml')->__('login'));
		$this->assignVariable('loginUrl', $this->getLoginUrl($settings));
		$this->assignVariable('ChangeLabel', Mage::helper('adminhtml')->__('change'));
		$this->assignVariable('ChangeUrl', $this->getUrl('*/*/index', array('key' => $this->getRequest()->get('key'), 'action' => Qualityunit_Liveagent_Block_Account::CHANGE_ACCOUNT_ACTION_FLAG)));
		$this->assignVariable('integrationSectionLabel', Mage::helper('adminhtml')->__('Integration'));
		$this->assignVariable('contactLink', Mage::helper('adminhtml')->__('contact us'));
		$this->assignVariable('contactHelp', Mage::helper('adminhtml')->__('Do you need any help with this plugin? Feel free to'));
	}
	
	private function getLoginUrl(Qualityunit_Liveagent_Helper_Settings $settings) {
	    try {
		    $authToken = $settings->getOwnerAuthToken();
	    } catch (Exception $e) {
	        $authToken = '';
	    }
		if ($authToken == '') {
			$sessionId = $settings->getOwnerSessionId();
			if ($sessionId != '') {
				return $settings->getLiveAgentUrl() . '/agent/index.php?S='.$settings->getOwnerSessionId();
			}
			return $settings->getLiveAgentUrl() . '/agent/index.php';
		}
		return $settings->getLiveAgentUrl() . '/agent/index.php?AuthToken='.$authToken;
	}

	protected function getTemplateString() {
		return '
		<form id="configForm" name="edit_form" action="{saveUrlAction}" method="post">
		<input name="form_key" type="hidden" value="{formKey}" />
        <input name="action" type="hidden" value="{saveButtonCodeFlag}"/>
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
        	<div class="entry-edit-head"><h4>{accountSectionLabel}</h4></div>
            <fieldset>
            	<table cellspacing="0" class="form-list">
            		<colgroup class="label"></colgroup>
            		<colgroup class="value"></colgroup>
            		<colgroup class="scope-label"></colgroup>
            		<colgroup class=""></colgroup>
            		<tbody>
            			<tr id="row_la_url">
            				<td class="label"><label for="row_la_url">{accountUrlLabel}:</label></td>
            				<td class="value">{la-url}&nbsp;&nbsp;&nbsp;&nbsp;
            				<a href="{loginUrl}" target="_balnk">{loginLabel}</a></button>
            				&nbsp;&nbsp;&nbsp;&nbsp;
            				<a href="{ChangeUrl}">{ChangeLabel}</a></button>
            				</td>
            				<td class="scope-label"></td><td></td>
            			</tr>
            			</tbody>
            	</table>
            </fieldset>
        </div>
        <div class="entry-edit">
        	<div class="entry-edit-head"><h4>{integrationSectionLabel}</h4></div>
            <fieldset>
            	<table cellspacing="0" class="form-list">
            		<colgroup class="label"></colgroup>
            		<colgroup class="value"></colgroup>
            		<colgroup class="scope-label"></colgroup>
            		<colgroup class=""></colgroup>
            		<tbody>
            			<tr id="la-config-button-code">
            				<td class="label"><label for="la-config-button-code">{buttonCodeLabel}:</label></td>
            				<td class="value">{la-config-button-code}
            				<p class="note"><span>{buttonCodeHelp}</span></p>
            				</td>
            				<td class="scope-label"></td><td></td>
            			</tr>
            			</tbody>
            	</table>
            </fieldset>
        </div>
        </form>
	';
	}
}
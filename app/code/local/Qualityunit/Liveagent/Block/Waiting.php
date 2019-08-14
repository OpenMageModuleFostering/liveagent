<?php
class Qualityunit_Liveagent_Block_Waiting extends Qualityunit_Liveagent_Block_Base {
	protected function _prepareLayout() {
		parent::_prepareLayout();
		$this->assignVariable('dialogCaption', Mage::helper('adminhtml')->__('LiveAgent - Live chat and helpdesk plugin for Magento'));
		$this->assignVariable('sectionCaption', Mage::helper('adminhtml')->__('Account Installation'));
		$this->assignVariable('completeUrlAction', $this->getUrl('*/*/post'));
		$this->assignVariable('installingText', Mage::helper('adminhtml')->__('Thank you! Your account will be online within next few minutes. Please wait...'));
		$this->assignVariable('confEmailText', Mage::helper('adminhtml')->__('You should recieve confirmation email with your account credentials shortly.'));
		$this->assignVariable('bitLongerText', Mage::helper('adminhtml')->__('Note: Sometimes account creation process may take a'));
		$this->assignVariable('bitLongerLink', Mage::helper('adminhtml')->__('bit longer'));
		$this->assignVariable('installText', Mage::helper('adminhtml')->__('Installing'));
		$this->assignVariable('formKey', Mage::getSingleton('core/session')->getFormKey());
		$this->assignVariable('initializingText', Mage::helper('adminhtml')->__('Initializing...'));
		
	}

	protected function getTemplateString() {
		return '
		<form id="liveagent_wait_form" name="edit_form" action="{completeUrlAction}" method="post">
	<input name="form_key" type="hidden" value="{formKey}" />
	<input name="action" type="hidden" value="r"/>
	</form>
		<div class="content-header">
	<table cellspacing="0">
	<tbody><tr>
	<td style="width:50%;"><h3 class="icon-head head-promo-catalog">{dialogCaption}</h3></td>	
	</tr>
	</tbody>
	</table>
	</div>	
	<div class="entry-edit">
	<div class="entry-edit-head"><h4>{sectionCaption}</h4></div>
	<fieldset>
	<table cellspacing="0" class="form-list">
	<tbody>
	<tr>
	<td>{installingText}</td>
	</tr>	
	<tr>
	<td>{confEmailText}</td>
	</tr>
	<tr>
	<td	></td>
	</tr>
	<tr>
	<td><div name="liveagent_wait_status" id="liveagent_wait_status" style="padding:10px;">{installText}...</div></td>
	</tr>
	<tr>
	<td></td>
	</tr>
	<tr>
	<td><i>{bitLongerText}</i><a href="http://support.qualityunit.com/knowledgebase/live-agent/integration/magento-plugin/frequently-asked-questions.html" target="_blank">{bitLongerLink}</a></td>
	</tr>	
	</tbody>
	</table>
	</fieldset>
	</div>
	<script type="text/javascript"><!--//--><![CDATA[//><!--
	setTimeout("document.getElementById(\'liveagent_wait_status\').innerHTML = \'{initializingText}\'", 10);
	var timer = 3000;
	var percentage = 4;
	for (i=0;i<24;i++) {
	setTimeout("document.getElementById(\'liveagent_wait_status\').innerHTML = \'{installText} " + percentage + "% ...\'", timer);
	timer+=1000;
	percentage+=4;
	}
	setTimeout("window.location.reload()", timer);
	//--><!]]></script>		
	';
	}
}
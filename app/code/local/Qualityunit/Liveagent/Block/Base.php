<?php

class Qualityunit_Liveagent_Block_Base extends Mage_Core_Block_Template {

	protected $variables = array();

	protected function curPageURL() {
		$pageURL = 'http';
		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
			$pageURL .= "s";
		}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}

		$pageURL = preg_replace("/\?.*$/", "", $pageURL);

		return $pageURL;
	}

	protected function getPasswordBox($name, $value) {
		return '<input type="password" id="'.$name.'" name="'.$name.'" value="'.$value.'" class=" input-text" />';
	}

	protected function getTextArea($name, $value, $rows = 1, $cols = 10, $additionalClass = '') {
		return '<textarea rows="'.$rows.'" cols="'.$cols.'" id="'.$name.'" name="'.$name.'" class="'.$additionalClass.'" />'.$value.'</textarea>';
	}

	protected function getInputBox($name, $value) {
		return '<input type="text" id="'.$name.'" name="'.$name.'" value="'.$value.'" class=" input-text" />';
	}

	protected function _toHtml() {
		$html = $this->getTemplateString();
		foreach ($this->variables as $name => $value) {
			$html = str_replace('{'.$name.'}', $value, $html);
		}
		return $html;
	}

	protected function _prepareLayout() {
		parent::_prepareLayout();
	}

	protected function assignVariable($name, $value) {
		$this->variables[$name] = $value;
	}

	protected function getTemplateString() {
		return '';
	}
}
<?php

class Qualityunit_Liveagent_Model_Mysql4_Liveagent extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
	{
		// Note that the liveagent_id refers to the key field in your database table.
		$this->_init('liveagent/liveagent', 'liveagent_id');
	}
}
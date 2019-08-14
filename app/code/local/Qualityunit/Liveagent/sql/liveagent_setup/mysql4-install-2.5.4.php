<?php
$installer = $this;
$installer->startSetup();
$installer->run("
		ALTER TABLE {$this->getTable('liveagentsettings')}
	    CHANGE `value` `value` text COLLATE 'utf8_general_ci' NULL AFTER `name`;		
		");
$installer->endSetup();
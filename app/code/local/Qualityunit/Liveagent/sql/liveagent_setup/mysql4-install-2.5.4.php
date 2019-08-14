<?php
$installer = $this;
$installer->startSetup();
try {
$installer->run("
		ALTER TABLE {$this->getTable('liveagentsettings')}
	    CHANGE `value` `value` text COLLATE 'utf8_general_ci' NULL AFTER `name`;
		");
} catch (Exception $e) {
    // something went wrong...
}
$installer->endSetup();
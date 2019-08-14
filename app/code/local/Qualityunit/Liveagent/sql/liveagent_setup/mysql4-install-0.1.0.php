<?php
$installer = $this;
$installer->startSetup();
$installer->run("
		DROP TABLE IF EXISTS {$this->getTable('liveagentbuttons')};
		CREATE TABLE {$this->getTable('liveagentbuttons')} (
		`liveagentbutton_id` int(11) unsigned NOT NULL auto_increment,
		`buttonid` char(8) NOT NULL,
		`userid` char(8) DEFAULT NULL,
		`name` text,
		`contenttype` char(1) DEFAULT NULL,
		`onlinestatus` char(1) DEFAULT NULL,
		`onlinecode` longtext,
		`offlinecode` longtext,
		`window_width` varchar(10) DEFAULT NULL,
		`window_height` varchar(10) DEFAULT NULL,
		`window_position` varchar(20) DEFAULT NULL,
		`impressions` int(11) DEFAULT '0',
		`chats` int(11) DEFAULT '0',
		`data1` varchar(255) DEFAULT NULL,
		`data2` varchar(255) DEFAULT NULL,
		`data3` varchar(255) DEFAULT NULL,
		`data4` varchar(255) DEFAULT NULL,
		`data5` varchar(255) DEFAULT NULL,
		PRIMARY KEY (`liveagentbutton_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		DROP TABLE IF EXISTS {$this->getTable('liveagentsettings')};
		CREATE TABLE {$this->getTable('liveagentsettings')} (
		`id` int(11) unsigned NOT NULL auto_increment,
		`name` varchar(255) NOT NULL,
		`value` varchar(255) DEFAULT NULL,
		PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		INSERT INTO  {$this->getTable('liveagentsettings')} (`name` ,`value`) VALUES ('la-settings_accountstatus',  'N');
		INSERT INTO  {$this->getTable('liveagentsettings')} (`name` ,`value`) VALUES ('la-settings_accountnotreachabletimes', '0');
		INSERT INTO  {$this->getTable('liveagentsettings')} (`name` ,`value`) VALUES ('la-settings_buttoncode', '');
		");
$installer->endSetup();
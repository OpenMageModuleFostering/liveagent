<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Juraj Simon
 *   @package WpLiveAgentPlugin
 *   @version 1.0.0
 *
 *   Licensed under GPL2
 */

class Qualityunit_Liveagent_Helper_Signup extends Qualityunit_Liveagent_Helper_Base {

	public function signup($name, $email, $domain, $password, $papVisitorId = '') {
		$request = new La_Rpc_ActionRequest('Dp_QualityUnit_La_Signup', 'createAccountRequest');
		$request->setUrl('http://members.qualityunit.com/scripts/server.php');
		$request->addParam('domain', $domain);
		$request->addParam('name', $name);
		$request->addParam('email', $email);
		$request->addParam('password', $password);
		$request->addParam('variationid', '3513230f');
		$request->addParam('PAPvisitorId', $papVisitorId);
		$request->addParam('source', 'magento');
		$request->sendNow();
		return $request->getStdResponse();
	}
}
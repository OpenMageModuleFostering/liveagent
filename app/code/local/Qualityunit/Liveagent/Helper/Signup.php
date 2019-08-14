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
	    $ch = curl_init();
	    $curl_post_data = array(
	            'variation_id' => '3513230f',
	            'customer_email' => $email,
	            'initial_api_key' => $password,
	            'customer_name' => $name,
	            'subdomain' => $domain,
	            'location_id' => 'magento',
	            'apikey' => 'jx5imiBhB6K12zui03YJL0lumHOr7S5T'
	    );
	    curl_setopt($ch,CURLOPT_URL,"https://signup.ladesk.com/api/index.php?handler=accounts&apikey=jx5imiBhB6K12zui03YJL0lumHOr7S5T");
	    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_POST, true);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $curl_post_data);
	    $curl_response=curl_exec($ch);
	    if ($curl_response === false) {
	        $info = curl_getinfo($ch);
	        curl_close($ch);
	        throw new Qualityunit_Liveagent_Exception_Base("Error during signup request. Additioanl info:" . var_export($info, true));
	    }
	    $info = curl_getinfo($ch);
	    curl_close($ch);
	    Mage::log('Response info: ' . var_export($info, true));
	    Mage::log('Raw repsonse: ' . $curl_response);
	    $decodedResponse = json_decode($curl_response);
	    if (!isset($decodedResponse->response)) {
	        throw new Qualityunit_Liveagent_Exception_Base("Signup request failed. Please try again...");
	    }
	    if ($info['http_code'] != 200) {
	        throw new Qualityunit_Liveagent_Exception_Base("Signup request failed: " . $decodedResponse->response->errormessage);
	    }
	    return $decodedResponse->response;
	}
}

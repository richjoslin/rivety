<?php
/**
 * communit.as
 * @copyright (C)2008 Jaybill McCarthy, All Rights Reserved.
 * @category communitas
 * @package communitas
 * @author Jaybill McCarthy
 * @link http://communit.as communit.as
 * @license http://communit.as/docs/license License
 */

/**
 *
 * @package communitas
 * @subpackage core_lib
 * @license http://communit.as/docs/license License 
 */

	class Cts_Amazon_SQS
	{
		
		var $_key        = "";
		var $_secret     = "";
		var $_server     = "http://queue.amazonaws.com/";
		var $_pathToCurl = "";
		var $_date       = null;
		var $_error      = null;
				
		var $queue_url;

		function Cts_Amazon_SQS($key, $secret, $queue_url = null)
		{
			$this->_key    = $key;
			$this->_secret = $secret;
			$this->queue_url = $queue_url;
			
		}

		function createQueue($queue_name, $default_timeout = 30)
		{
			if ($default_timeout < 30){ 
				$default_timeout = 30;	
			}
			$params = array("QueueName" => $queue_name, "DefaultVisibilityTimeout" => $default_timeout);
			$xml = $this->go("CreateQueue", $params);
			if($xml === false){
				return false;
			} 

			return strval($xml->QueueUrl);
		}

		function listQueues($queue_name_prefix = "")
		{
			$params = ($queue_name_prefix == "") ? array() : array("QueueNamePrefix" => $queue_name_prefix);
			$xml = $this->go("ListQueues", $params);
			if($xml === false) {
				return false;	
			} else {
			$out = array();
				foreach($xml->ListQueuesResult->QueueUrl as $url){
					$out[] = strval($url);
				}
				return $out;
			}
		}

		function deleteQueue($queue_url = null)
		{
			if(!isset($queue_url)){
				$queue_url = $this->queue_url;
			}
			$xml = $this->go("DeleteQueue", null, $queue_url);
			return $xml ? true : false;
		}

		function sendMessage($message_body, $queue_url = null)
		{
			if(!isset($queue_url)){
				$queue_url = $this->queue_url;	
			}
			$params = array("MessageBody" => $message_body);
			$xml = $this->go("SendMessage", $params, $queue_url);
			if($xml === false){
				return false;	
			} else {				
				Cts_Log::report("sent message", $xml->SendMessageResult->MessageId,Zend_Log::INFO);
				return strval($xml->SendMessageResult->MessageId);
			}
		}

		function receiveMessage($number = 1, $timeout = null, $queue_url = null)
		{
			if(!isset($queue_url)){
				$queue_url = $this->queue_url;
			} 

			$number = intval($number);
			if($number < 1) {
				$number = 1;	
			} elseif($number > 256){
				$number = 256;
			}

			$params = array();
			$params['MaxNumberOfMessages'] = $number;
			if(isset($timeout)){
				$params['VisibilityTimeout'] = intval($timeout);	
			}

			$xml = $this->go("ReceiveMessage", $params, $queue_url);

			if($xml === false){
				Cts_Log::report('sqs cannot recieve messages',null,Zend_Log::ERR);				
				return false;
			} else {
				Cts_Log::report('sqs recieved messages',$xml,Zend_Log::INFO);
				$out = array();
				foreach($xml->ReceiveMessageResult->Message as $m){
					$out[] = array("MessageId" => strval($m->MessageId), "MessageBody" => urldecode(strval($m->Body)));
				}
				return $out;
			}
		}

		function deleteMessage($message_id, $queue_url = null)
		{
			if(!isset($queue_url)) $queue_url = $this->queue_url;
			$params = array("MessageId" => $message_id);
			$xml = $this->go("DeleteMessage", $params, $queue_url);
			return ($xml === false) ? false : true;
		}
		
		function clearQueue($limit = 100, $queue_url)
		{
			$m = $this->receiveMessage($limit, null, $queue_url);
			foreach($m as $n){
				$this->deleteMessage($n['MessageId'], $queue_url);
			}
		}

		function setTimeout($timeout, $queue_url = null)
		{
			$timeout = intval($timeout);
			if(!isset($queue_url)) {
				$queue_url = $this->queue_url;	
			}
			if(!is_int($timeout)){
				$timeout = 30;	
			}
			$params = array("Attribute.Name" => "VisibilityTimeout", "Attribute.Value" => $timeout);
			$xml = $this->go("SetQueueAttributes", $params, $queue_url);
			return ($xml === false) ? false : true;
		}

		function getTimeout($queue_url = null)
		{
			if(!isset($queue_url)){
				$queue_url = $this->queue_url;	
			}
			$params = array("AttributeName" => "VisibilityTimeout");
			$xml = $this->go("GetQueueAttributes", $params, $queue_url);
			return ($xml === false) ? false : strval($xml->GetQueueAttributesResult->Attribute->Value);
		}
		function getSize($queue_url = null)
		{
			if(!isset($queue_url)){
				$queue_url = $this->queue_url;	
			}
			$params = array("AttributeName" => "ApproximateNumberOfMessages");			
			$xml = $this->go("GetQueueAttributes", $params, $queue_url);
			return ($xml === false) ? false : strval($xml->GetQueueAttributesResult->Attribute->Value);
		}
		function setQueue($queue_url)
		{
			$this->queue_url = $queue_url;
		}

		function go($action, $params, $url = null)
		{
			$params['Action'] = $action;
			
			if(!$url) $url = $this->_server;
			
			$params['AWSAccessKeyId'] = $this->_key;
			$params['SignatureVersion'] = 1;
			$params['Timestamp'] = gmdate("Y-m-d\TH:i:s\Z");
			$params['Version'] = "2008-01-01";
			uksort($params, "strnatcasecmp");

			$toSign = "";
			foreach($params as $key => $val){
				$toSign .= $key . $val;
			}
			$sha1 = $this->hasher($toSign);
			$sig  = $this->base64($sha1);
			$params['Signature'] = $sig;

			Cts_Log::report('sqs go params',$params,Zend_Log::INFO);
			$output = Cts_Url::get ($url,$params);
			
			$xmlstr =$output['output'];
			Cts_Log::report("output from sqs", $output,Zend_Log::DEBUG);
			try{
				$xml = new SimpleXMLElement($xmlstr);
				
				if($output['http_code'] == 200 and !isset($xml->Errors)){			
					Cts_Log::report("xml from sqs", $xml,Zend_Log::DEBUG);
					return $xml;
				} else {
					return false;
				}
			} catch(Exception $ex) {
				return false;
			}
		}		
		
		function hasher($data)
		{
			// Algorithm adapted (stolen) from http://pear.php.net/package/Crypt_HMAC/)
			$key = $this->_secret;
			if(strlen($key) > 64)
				$key = pack("H40", sha1($key));
			if(strlen($key) < 64)
				$key = str_pad($key, 64, chr(0));
			$ipad = (substr($key, 0, 64) ^ str_repeat(chr(0x36), 64));
			$opad = (substr($key, 0, 64) ^ str_repeat(chr(0x5C), 64));
			return sha1($opad . pack("H40", sha1($ipad . $data)));
		}

		function base64($str)
		{
			$ret = "";
			for($i = 0; $i < strlen($str); $i += 2)
				$ret .= chr(hexdec(substr($str, $i, 2)));
			return base64_encode($ret);
		}

	}
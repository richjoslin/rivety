<?php
/** 
 *  PHP Version 5
 *
 *  @category    Amazon
 *  @package     Amazon_FPS
 *  @copyright   Copyright 2008 Amazon Technologies, Inc.
 *  @link        http://aws.amazon.com
 *  @license     http://aws.amazon.com/apache2.0  Apache License, Version 2.0
 *  @version     2008-09-17
 */
/******************************************************************************* 
 *    __  _    _  ___ 
 *   (  )( \/\/ )/ __)
 *   /__\ \    / \__ \
 *  (_)(_) \/\/  (___/
 * 
 *  Amazon FPS PHP5 Library
 *  Generated: Wed Sep 23 03:35:04 PDT 2009
 * 
 */

class RivetyCore_Amazon_FPS_SignatureUtilsForOutbound {
	 
    const SIGNATURE_KEYNAME = "signature";
    const SIGNATURE_METHOD_KEYNAME = "signatureMethod";
    const SIGNATURE_VERSION_KEYNAME = "signatureVersion";
    const SIGNATURE_VERSION_1 = "1";
    const SIGNATURE_VERSION_2 = "2";
    const CERTIFICATE_URL_KEYNAME = "certificateUrl";
    const EMPTY_STRING = "";

	//cache of the public key so that it need not be fetched every time!
	static $public_key_cache = array();

	//Your AWS access key	
	private $aws_access_key;

	//Your AWS secret key. Required only for ipn or return url verification signed using signature version1.	
	private $aws_secret_key;

    public function __construct($aws_access_key = null, $aws_secret_key = null) {
        $this->aws_access_key = $aws_access_key;
        $this->aws_secret_key = $aws_secret_key;
    }
	
    /**
     * Validates the request by checking the integrity of its parameters.
     * @param parameters - all the http parameters sent in IPNs or return urls. 
     * @param urlEndPoint should be the url which recieved this request. 
     * @param httpMethod should be either POST (IPNs) or GET (returnUrl redirections)
     */
    public function validateRequest(array $parameters, $urlEndPoint, $httpMethod)  {
        $signatureVersion = $parameters[self::SIGNATURE_VERSION_KEYNAME];
        if (self::SIGNATURE_VERSION_2 == $signatureVersion) {
            return $this->validateSignatureV2($parameters, $urlEndPoint, $httpMethod);
        } else {
            return $this->validateSignatureV1($parameters);
        }
    }

    /**
     * Verifies the signature using HMAC and your secret key. 
     */
    private function validateSignatureV1(array $parameters) {
	    $signature = $parameters[self::SIGNATURE_KEYNAME];
	    //We should not include signature while calculating string to sign.
	    unset($parameters[self::SIGNATURE_KEYNAME]);
	    
	    $stringToSign = self::_calculateStringToSignV1($parameters);
	    //We should include signature back to array after calculating string to sign.
	    $parameters[self::SIGNATURE_KEYNAME] = $signature;
	        
        return $signature == base64_encode(hash_hmac('sha1', $stringToSign, $this->aws_secret_key, true));
    }
	
    /**
     * Verifies the signature using PKI. 
     * Only default algorithm OPENSSL_ALGO_SHA1 is supported.
     */
    private function validateSignatureV2(array $parameters, $urlEndPoint, $httpMethod) {
	    //1. Input validation
	    $signature = $parameters[self::SIGNATURE_KEYNAME];
	    if (!isset($signature)) {
	    	throw new Exception("'signature' is missing from the parameters.");
	    }
	    
	    $signatureMethod = $parameters[self::SIGNATURE_METHOD_KEYNAME];
	    if (!isset($signatureMethod)) {
	    	throw new Exception("'signatureMethod' is missing from the parameters.");
	    }
	    $signatureAlgorithm = self::getSignatureAlgorithm($signatureMethod);
	    if (!isset($signatureAlgorithm)) {
	    	throw new Exception("'signatureMethod' present in parameters is invalid. Valid values are: RSA-SHA1");
	    }
	    
	    $certificateUrl = $parameters[self::CERTIFICATE_URL_KEYNAME];
	    if (!isset($certificateUrl)) {
	    	throw new Exception("'certificateUrl' is missing from the parameters.");
	    }
	    $publicKey = self::getPublicKey($certificateUrl);
	    if (!isset($publicKey)) {
	    	throw new Exception("public key certificate could not fetched from url: " . $certificateUrl);
	    }

		//2. Calculate string to sign
	    $hostHeader = self::getHostHeader($urlEndPoint);
	    $requestURI = self::getRequestURI($urlEndPoint);
	    
	    //We should not include signature while calculating string to sign.
	    unset($parameters[self::SIGNATURE_KEYNAME]); 
	    $stringToSign = self::_calculateStringToSignV2($parameters, $httpMethod, $hostHeader, $requestURI);
	    //We should include signature back to array after calculating string to sign.
	    $parameters[self::SIGNATURE_KEYNAME] = $signature;

		//3. Verification of signature	        
	    $decoded_signature = base64_decode($signature);
    	return openssl_verify($stringToSign, $decoded_signature, $publicKey);
    	//return openssl_verify($stringToSign, $signature, $publicKey, OPENSSL_ALGO_SHA1);
    }

    /**
     * Calculate String to Sign for SignatureVersion 1
     * @param array $parameters request parameters
     * @return String to Sign
     */
    private static function _calculateStringToSignV1(array $parameters) {
        $data = '';
        uksort($parameters, 'strcasecmp');
        foreach ($parameters as $parameterName => $parameterValue) {
            $data .= $parameterName . $parameterValue;
        }
        return $data;
    }

    /**
     * Calculate String to Sign for SignatureVersion 2
     * @param array $parameters request parameters
     * @return String to Sign
     */
    private static function _calculateStringToSignV2(array $parameters, $httpMethod, $hostHeader, $requestURI) {
        if ($httpMethod == null) {
        	throw new Exception("HttpMethod cannot be null");
        }
        $data = $httpMethod;
        $data .= "\n";
        
        if ($hostHeader == null) {
        	$hostHeader = "";
        } 
        $data .= $hostHeader;
        $data .= "\n";
        
        if (!isset ($requestURI)) {
        	$requestURI = "/";
        }
		$uriencoded = implode("/", array_map(array("RivetyCore_Amazon_FPS_SignatureUtilsForOutbound", "_urlencode"), explode("/", $requestURI)));
        $data .= $uriencoded;
        $data .= "\n";
        
        uksort($parameters, 'strcmp');
        $data .= self::_getParametersAsString($parameters);
        return $data;
    }

	private static function  getHostHeader($endPoint) {
		$url = parse_url($endPoint);
		$host = $url['host'];
		$scheme = strtoupper($url['scheme']);
		if (isset($url['port'])) {
			$port = $url['port'];
			if (("HTTPS" == $scheme && $port != 443) ||  ("HTTP" == $scheme && $port != 80)) {
				return strtolower($host) . ":" . $port;
			}
		}
		return strtolower($host);
	}

    private static function getRequestURI($endPoint) {
		$url = parse_url($endPoint);
        $requestURI = $url['path'];
        if ($requestURI == null || $requestURI == self::EMPTY_STRING) {
            $requestURI = "/";
        } else {
            $requestURI = urlDecode($requestURI);
        }
        return $requestURI;
    }

    private static function getSignatureAlgorithm($signatureMethod) {
        if ("RSA-SHA1" == $signatureMethod) {
            return OPENSSL_ALGO_SHA1;
        }
        return null;
    }

    private static function getPublicKey($certificateUrl) {
		//if found in cache, return
		if (isset(self::$public_key_cache[$certificateUrl])) {
			return self::$public_key_cache[$certificateUrl];
		}
		
		//fetch the certificate and cache it
	    $options = array(
		    CURLOPT_SSL_VERIFYHOST => true,
			CURLOPT_SSL_VERIFYPEER => true, //verify the certificate
			CURLOPT_CAINFO => "../ca-bundle.crt",
			CURLOPT_RETURNTRANSFER => true,     // return web page
			CURLOPT_FOLLOWLOCATION => false,     // do not follow redirects
	    );
	    
	    $ch = curl_init($certificateUrl);
	    curl_setopt_array( $ch, $options );
	    $content = curl_exec( $ch );
	    $err     = curl_errno( $ch );
	    $errmsg  = curl_error( $ch );
	    $header  = curl_getinfo( $ch );
	    curl_close( $ch );
	    
	    $header['errno']   = $err;
	    $header['errmsg']  = $errmsg;
	    $header['content'] = $content;
	    $public_key = openssl_get_publickey($content);
	    self::$public_key_cache[$certificateUrl] = $public_key;
	    return $public_key;
    }

    private static function _urlencode($value) {
		return str_replace('%7E', '~', rawurlencode($value));
    }

    /**
     * Convert paremeters to Url encoded query string
     */
    public static function _getParametersAsString(array $parameters) {
        $queryParameters = array();
        foreach ($parameters as $key => $value) {
            $queryParameters[] = $key . '=' . self::_urlencode($value);
        }
        return implode('&', $queryParameters);
    }

    private static function urlDecode($value) {
        $decoded = null;
        $decoded = rawurldecode($value);
        return $decoded;
    }
}


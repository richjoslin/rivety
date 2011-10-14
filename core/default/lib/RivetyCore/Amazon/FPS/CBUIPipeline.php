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

/**
 * Base class for CBUI pipelines. 
 */
abstract class RivetyCore_Amazon_FPS_CBUIPipeline {

	const SIGNATURE_KEYNAME = "signature";
	const SIGNATURE_METHOD_KEYNAME = "signatureMethod";
	const SIGNATURE_VERSION_KEYNAME = "signatureVersion";
	const HMAC_SHA1_ALGORITHM = "HmacSHA1";
	const HMAC_SHA256_ALGORITHM = "HmacSHA256";

    const HTTP_GET_METHOD = "GET";

    /**
     * The default URL corresponds to production environment. Change the URL for sandbox environment.
     */ 
    protected static $CBUI_URL = "https://authorize.payments.amazon.com/cobranded-ui/actions/start";

    /**
     * Array to store the name value pairs of the request
     */
    private $parameters = array();

    /**
     * Version parameter for consistent signature for incoming and outgoing requests
     */
    protected static $VERSION = "2009-01-09";
    
    /**
     * Version parameter for consistent signature for incoming and outgoing requests
     */
    protected static $SIGNATURE_VERSION = 2;
    
    /**
     * Version parameter for consistent signature for incoming and outgoing requests
     */
    protected static $SIGNATURE_METHOD = self::HMAC_SHA256_ALGORITHM;

    /**
     * @param string $accessKeyId    Amazon Web Services Access Key ID.
     * @param string $secretAccessKey   Amazon Web Services Secret Access Key.
     */
    function RivetyCore_Amazon_FPS_CBUIPipeline($pipelineName, $awsAccessKey, $awsSecretKey) {
        $this->awsSecretKey = $awsSecretKey;
        $this->awsAccessKey = $awsAccessKey;
        
        //Add default parameters
        $this->addParameter("callerKey", $awsAccessKey);
        $this->addParameter("pipelineName", $pipelineName);
        $this->addParameter("version", self::$VERSION);
        $this->addParameter("signatureVersion", self::$SIGNATURE_VERSION);
        $this->addParameter("signatureMethod", self::$SIGNATURE_METHOD);
    }

    /**
     * Adds any custom name value pair to the query string
     *
     * @param string $key    Key of the key-value pair in querystring
     * @param string $value  Value of the key-value pair in querystring
     */
    public function addParameter($key, $value) {
        $this->parameters[$key] = $value;
    }

    /**
     * Adds all the parameters to existing parameters. 
     *
     * @param string $params    Optional parameters.
     */
    public function addOptionalParameters($params) {
        foreach ($params as $key => $value) {
            $this->parameters[$key] = $value;
        }
    }

    /**
     * Computes RFC 2104-compliant HMAC signature for request parameters
     * Implements AWS Signature, as per following spec:
     *
     * If Signature Version is 1, it performs the following:
     *
     * Sorts all  parameters (including SignatureVersion and excluding Signature,
     * the value of which is being created), ignoring case.
     *
     * Iterate over the sorted list and append the parameter name (in original case)
     * and then its value. It will not URL-encode the parameter values before
     * constructing this string. There are no separators.
     *
     * If Signature Version is 2, string to sign is based on following:
     *
     *    1. The HTTP Request Method followed by an ASCII newline (%0A)
     *    2. The HTTP Host header in the form of lowercase host, followed by an ASCII newline.
     *    3. The URL encoded HTTP absolute path component of the URI
     *       (up to but not including the query string parameters);
     *       if this is empty use a forward '/'. This parameter is followed by an ASCII newline.
     *    4. The concatenation of all query string components (names and values)
     *       as UTF-8 characters which are URL encoded as per RFC 3986
     *       (hex characters MUST be uppercase), sorted using lexicographic byte ordering.
     *       Parameter names are separated from their values by the '=' character
     *       (ASCII character 61), even if the value is empty.
     *       Pairs of parameter and values are separated by the '&' character (ASCII code 38).
     *
     */
    public function signParameters(array $parameters, $httpMethod, $host, $requestURI) {
        $signatureVersion = $parameters[self::SIGNATURE_VERSION_KEYNAME];
        $algorithm = self::HMAC_SHA1_ALGORITHM;
        $stringToSign = null;
        if (2 === $signatureVersion) {
            $algorithm = $parameters[self::SIGNATURE_METHOD_KEYNAME];
            $stringToSign = self::_calculateStringToSignV2($parameters, $httpMethod, $host, $requestURI);
        } else {
            $stringToSign = self::_calculateStringToSignV1($parameters);
        }
        return self::_sign($stringToSign, $this->awsSecretKey, $algorithm);
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
		$uriencoded = implode("/", array_map(array("RivetyCore_Amazon_FPS_CBUIPipeline", "_urlencode"), explode("/", $requestURI)));
        $data .= $uriencoded;
        $data .= "\n";
        
        uksort($parameters, 'strcmp');
        $data .= self::_getParametersAsString($parameters);
        return $data;
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

    /**
     * Computes RFC 2104-compliant HMAC signature.
     */
    private static function _sign($data, $key, $algorithm) {
        if ($algorithm === 'HmacSHA1') {
            $hash = 'sha1';
        } else if ($algorithm === 'HmacSHA256') {
            $hash = 'sha256';
        } else {
            throw new Exception ("Non-supported signing method specified");
        }
        return base64_encode(
            hash_hmac($hash, $data, $key, true)
        );
    }

    /**
     * Construct the pipeline request url using given parameters. 
     * Computes signature and adds it as additional parameter.
     * @param parameters - Map of pipeline request parameters.
     * @return Returns the pipeline request url. 
     * @throws MalformedURLException
     * @throws SignatureException
     * @throws UnsupportedEncodingException
     */
    private function constructUrl($parameters) {

        if ($parameters == null) {
            throw new Exception("Parameters can not be empty.");
        }

        $hostHeader = $this->getHostHeader(self::$CBUI_URL);
        $requestURI = $this->getRequestURI(self::$CBUI_URL);

        $signature = $this->signParameters($parameters, self::HTTP_GET_METHOD, $hostHeader, $requestURI);
        $parameters["signature"] = $signature;

        $queryString = http_build_query($parameters, '', '&');
        
        return self::$CBUI_URL . "?" . $queryString;
    }
    
    private function  getHostHeader($endPoint) {
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

    private function getRequestURI($endPoint) {
		$url = parse_url($endPoint);
        $requestURI = $url['path'];
        if (!isset($requestURI)) {
            $requestURI = "/";
        } else {
            $requestURI = urlDecode($requestURI);
        }
        return $requestURI;
    }

    protected function validateCommonMandatoryParameters($parameters) {
        if (!isset($parameters["pipelineName"])) {
            throw new Exception("pipelineName is missing in parameters.");
        }
        
        if (!isset($parameters["version"])) {
            throw new Exception("version is missing in parameters.");
        }

        if (!isset($parameters["returnURL"])) {
            throw new Exception("returnURL is missing in parameters.");
        }

        if (!isset($parameters["callerReference"])) {
            throw new Exception("callerReference is missing in parameters.");
        }
    }

    abstract protected function validateParameters($parameters);
    
    /**
     * Constructs the query string for the parameters added to this class
     *
     * This function also calculates the signature of the all the name value pairs
     * added to the class. 
     *
     * @return string  URL 
     */
    public function getURL() {
        $this->validateCommonMandatoryParameters($this->parameters);
		$this->validateParameters($this->parameters);
		return $this->constructUrl($this->parameters);
    }
}

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

class RivetyCore_Amazon_FPS_CBUISingleUsePipeline extends RivetyCore_Amazon_FPS_CBUIPipeline {

    /**
     * @param string $accessKeyId    Amazon Web Services Access Key ID.
     * @param string $secretAccessKey   Amazon Web Services Secret Access Key.
     */
    function RivetyCore_Amazon_FPS_CBUISingleUsePipeline($awsAccessKey, $awsSecretKey) {
        parent::RivetyCore_Amazon_FPS_CBUIPipeline("SingleUse", $awsAccessKey, $awsSecretKey);
    }

    /**
     * Set mandatory parameters required for single use token pipeline.
     */
    function setMandatoryParameters($callerReference, $returnUrl, $transactionAmount) {
        $this->addParameter("callerReference", $callerReference);
        $this->addParameter("returnURL", $returnUrl);
        $this->addParameter("transactionAmount", $transactionAmount);
    }

    function validateParameters($parameters) {
        //mandatory parameters for single use pipeline
        if (!isset($parameters["transactionAmount"])) {
            throw new Exception("transactionAmount is missing in parameters.");
        }
    }

}

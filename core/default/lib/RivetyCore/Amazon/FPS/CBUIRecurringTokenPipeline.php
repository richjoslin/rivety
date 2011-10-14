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



class RivetyCore_Amazon_FPS_CBUIRecurringTokenPipeline extends RivetyCore_Amazon_FPS_CBUIPipeline {

    /**
     * @param string $accessKeyId    Amazon Web Services Access Key ID.
     * @param string $secretAccessKey   Amazon Web Services Secret Access Key.
     */
    function RivetyCore_Amazon_FPS_CBUIRecurringTokenPipeline($awsAccessKey, $awsSecretKey) {
        parent::RivetyCore_Amazon_FPS_CBUIPipeline("Recurring", $awsAccessKey, $awsSecretKey);
    }

    /**
     * Set mandatory parameters required for recurring token pipeline.
     */
    function setMandatoryParameters($callerReference, $returnUrl, 
    		$transactionAmount, $recurringPeriod) {
        $this->addParameter("callerReference", $callerReference);
        $this->addParameter("returnURL", $returnUrl);
        $this->addParameter("transactionAmount", $transactionAmount);
        $this->addParameter("recurringPeriod", $recurringPeriod);
    }

    function validateParameters($parameters) {
        //mandatory parameters for recurring token pipeline
        if (!isset($parameters["transactionAmount"])) {
            throw new Exception("transactionAmount is missing in parameters.");
        }

        if (!isset($parameters["recurringPeriod"])) {
            throw new Exception("recurringPeriod is missing in parameters.");
        }
    }

}

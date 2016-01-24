<?php

namespace Wassa\MPS;
use Psr\Log\LoggerInterface;

/**
 * IosFeedback
 */
class IosFeedback
{
    private $certificateTempResource;
    private $environment;
    private $logger;

    /*
     * Constructor
     */
    public function __construct($certificate, $environment, LoggerInterface $loggerReceiver)
    {
        list($this->certificateTempResource, ) = IosUtils::createTempCertFiles($certificate);
        $env = strtolower($environment);

        if ($env != "sandbox" && $env != "production") {
            throw new PushException("L'environement doit être 'production' ou 'sandbox'");
        }
        else {
            $this->environment = ($env == 'sandbox' ? \ApnsPHP_Abstract::ENVIRONMENT_SANDBOX : \ApnsPHP_Abstract::ENVIRONMENT_PRODUCTION);
        }

        $this->logger = new Logger($loggerReceiver);
    }

    public function check()
    {
        // Instanciate a new ApnsPHP_Feedback object
        $feedback = new \ApnsPHP_Feedback($this->environment, IosUtils::getTempFileName($this->certificateTempResource));

        $feedback->setLogger($this->logger);

        // Connect to the Apple Push Notification Feedback Service
        $feedback->connect();

        try {
            $aDeviceTokens = $feedback->receive();
        }
        catch(\Exception $e) {
            throw new PushException('Impossible de recevoir la liste de tokens du Feedback service');
        }

        // Disconnect from the Apple Push Notification Feedback Service
        $feedback->disconnect();

        return $this->getOldTokens($aDeviceTokens);
    }

    private function getOldTokens($oldTokens)
    {
        if(!empty($oldTokens)) {
            $tokens = array();

            foreach($oldTokens as $oldToken) {
                $tokens[] = $oldToken['deviceToken'];
            }

            return $tokens;
        }
        else {
            return array();
        }
    }
}

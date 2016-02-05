<?php

namespace Wassa\MPS;

use Psr\Log\LoggerInterface;

/**
 * IosPush
 */
class IosPush extends AbstractPush
{
    /**
     * @var string
     */
    private $certificate;

    /**
     * @inheritdoc
     */
    public function __construct(PushData $pushData, $registrationTokens, $parameters, LoggerInterface $logger)
    {
        parent::__construct($pushData, $registrationTokens, $parameters, $logger);

        if (!isset($parameters['environment'])) {
            throw new PushException("No environment specified");
        }
        else {
            $env = strtolower($parameters['environment']);

            if ($env == 'sandbox') {
                $this->parameters['environment'] = \ApnsPHP_Abstract::ENVIRONMENT_SANDBOX;

                if (isset($parameters['sand_cert']) && file_exists($parameters['sand_cert'])) {
                    $this->certificate = $parameters['sand_cert'];
                }
                else {
                    throw new PushException("No APNS sandbox certificate file specified or file doesn't exist");
                }
            }
            elseif($env == 'production') {
                $this->parameters['environment'] = \ApnsPHP_Abstract::ENVIRONMENT_PRODUCTION;

                if (isset($parameters['prod_cert']) && file_exists($parameters['prod_cert'])) {
                    $this->certificate = $parameters['prod_cert'];
                }
                else {
                    throw new PushException("No APNS production certificate specified");
                }
            }
            else {
                throw new PushException("APNS environment must be 'production' or 'sandbox'");
            }
        }

        if (!isset($parameters['ca_cert'])) {
            throw new PushException("No APNS root CA certificate specified");
        }
    }

    /**
     * @inheritdoc
     */
    public function sendPush()
    {
        $failedMessages = [];
        
        if (!isset($this->registrationTokens) || !count($this->registrationTokens)) {
            $failedMessages = $this->registrationTokens;

            return $this->returnResult($failedMessages, $this->registrationTokens);
        }

        // Instanciate a new ApnsPHP_Push object
        $push = new \ApnsPHP_Push($this->parameters['environment'], $this->certificate);

        // Set the Root Certificate Autority to verify the Apple remote peer
        $push->setRootCertificationAuthority($this->parameters['ca_cert']);

        $push->setLogger($this->logger);

        // Connect to the Apple Push Notification Service
        $push->connect();

        foreach ($this->registrationTokens as $token) {
            try {
                $message = new \ApnsPHP_Message($token);
            }
            catch (\Exception $e) {
                $this->logger->log("ERROR: Device token " . $token . " has problem : " . $e->getMessage());
                $failedMessages[] = $token;
                
                continue;
            }

            $badge = $this->pushData->getApnsBadge();
            $category = $this->pushData->getApnsCategory();
            $expiry = $this->pushData->getApnsExpiry();
            $text = $this->pushData->getApnsText();
            $sound = $this->pushData->getApnsSound();
            $customProperties = $this->pushData->getApnsCustomProperties();

            if(isset($badge)) {
                $message->setBadge($badge);
            }

            if(isset($category)) {
                $message->setCategory($category);
            }

            if(isset($expiry)) {
                $message->setExpiry($expiry);
            }

            if(isset($text)) {
                $message->setText($text);
            }

            if(isset($sound)) {
                $message->setSound($sound);
            }
            else {
                $message->setSound();
            }

            if (isset($customProperties) && is_array($customProperties)) {
                foreach ($customProperties as $name => $value) {
                    $message->setCustomProperty($name, is_scalar($value) ? $value : json_encode($value));
                }
            }

            // Add the message to the message queue
            $push->add($message);
        }

        // Send all messages in the message queue
        try {
            $push->send();

            // Disconnect from the Apple Push Notification Service
            $push->disconnect();

            // Examine the error message container
            $aErrorQueue = $push->getErrors();
            if (!empty($aErrorQueue)) {
                foreach($aErrorQueue as $error) {
                    // On récupère la liste des token qui ont généré une erreur
                    $var = $error['MESSAGE'];
                    $failedMessages = array_merge($failedMessages, $var->getRecipients());
                }
            }

            return $this->returnResult($failedMessages, $this->registrationTokens);
        }
        catch (\Exception $e) {
            return $this->returnResult($this->registrationTokens, $this->registrationTokens);
        }
    }
}

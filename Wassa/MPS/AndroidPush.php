<?php

namespace Wassa\MPS;
use Psr\Log\LoggerInterface;

/**
 * AndroidPush
 *
 * @package Wassa\MPS
 *
 * This class sends Android (GCM) push notifications
 *
 */
class AndroidPush extends AbstractPush
{
    /**
     * @inheritdoc
     */
    public function __construct(PushData $pushData, $registrationTokens, $parameters, LoggerInterface $logger)
    {
        parent::__construct($pushData, $registrationTokens, $parameters, $logger);
    }

    /**
     * @return bool
     */
    public function sendPush()
    {
        try
        {
            $gcmPhp = new \Wassa\GcmPhp\Gcm();
            $response = $gcmPhp->sendMessage($this->parameters['api_key'],
                $this->parameters['dry_run'],
                $this->registrationTokens,
                $this->pushData->getGcmPayloadData(),
                $this->pushData->getGcmCollapseKey());

            if (!$response) {
                $this->logger->log('ERROR: Unable to parse response');

                return false;
            }
            else {
                $okMessages = [];
                $regIdsCount = count($this->registrationTokens);

                for ($i = 0; $i < $regIdsCount; $i++) {
                    $regId = $this->registrationTokens[$i];
                    $responseItem = $response->results[$i];

                    if (isset($responseItem->message_id)) {
                        $okMessages[] = $regId;
                    }
                }

                return $this->returnResult($okMessages, $this->registrationTokens, false);
            }
        }
        catch(\Exception $e)
        {
            //$this->failedMessages[] = $registrationId;
            $this->logger->log('ERROR: ' . $e->getMessage());
        }

        return $this->returnResult([], $this->registrationTokens, false);
    }
}

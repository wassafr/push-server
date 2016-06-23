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
    const BATCH_CHUNK_SIZE = 750;

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
        try {
            $gcmPhp = new \Wassa\GcmPhp\Gcm();

            // Separate devices into chunks due to GCM limitation per request
            $devicesChunks = array_chunk($this->registrationTokens, self::BATCH_CHUNK_SIZE);

            $chunkResults = array();
            foreach ($devicesChunks as $registrationsTokens) {
                $response = $gcmPhp->sendMessage($this->parameters['api_key'],
                    $this->parameters['dry_run'],
                    $registrationsTokens,
                    $this->pushData->getGcmPayloadData(),
                    $this->pushData->getGcmCollapseKey());

                if (!$response) {
                    $this->logger->log('ERROR: Unable to parse response');

                    return false;
                } else {
                    $okMessages = [];
                    $regIdsCount = count($this->registrationTokens);

                    for ($i = 0; $i < $regIdsCount; $i++) {
                        $regId = $this->registrationTokens[$i];
                        $responseItem = $response->results[$i];

                        if (isset($responseItem->message_id)) {
                            $okMessages[] = $regId;
                        }
                    }

                    $chunkResults = $this->returnResult($okMessages, $this->registrationTokens, false);
                }
            }

            // Merge chunks results into a single result
            $result = array();
            foreach ($chunkResults as $chunkResult) {
                // If no result, set current chunkResult
                if (empty($result)) {
                    $result = $chunkResult;
                } else {
                    // Else merge chunk results with result
                    $result['success'] = array_merge($result['success'], $chunkResult['success']);
                    $result['error'] = array_merge($result['error'], $chunkResult['error']);
                    $result['all_ok'] &= $chunkResult['all_ok'];
                }
            }
            return $result;

        } catch (\Exception $e) {
            //$this->failedMessages[] = $registrationId;
            $this->logger->log('ERROR: ' . $e->getMessage());
        }


        return $this->returnResult([], $this->registrationTokens, false);
    }
}
